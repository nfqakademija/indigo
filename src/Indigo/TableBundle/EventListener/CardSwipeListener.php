<?php

namespace Indigo\TableBundle\EventListener;

use Doctrine\ORM\EntityManagerInterface;
use Indigo\GameBundle\Entity\Game;
use Indigo\GameBundle\Entity\GameTime;
use Indigo\GameBundle\Entity\PlayerTeamRelation;
use Indigo\GameBundle\Entity\TableStatus;
use Indigo\GameBundle\Entity\TableStatusRepository;
use Indigo\GameBundle\Entity\Team;
use Indigo\GameBundle\Event\GameEvents;
use Indigo\GameBundle\Event\GameFinishEvent;
use Indigo\GameBundle\Repository\GameStatusRepository;
use Indigo\GameBundle\Repository\GameTypeRepository;
use Indigo\GameBundle\Service\TeamCreate;
use Indigo\TableBundle\Event\TableEvent;
use Indigo\TableBundle\Model\CardSwipeModel;
use Indigo\UserBundle\Entity\User as Player;
use Indigo\UserBundle\Service\Registration;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class CardSwipeListener
{
    const DOUBLE_SWIPE_IN = 5;
    const DOUBLE_SWIPE_MIN_TS = 1;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var EventDispatcherInterface
     */
    private $ed;

    /**
     * @var TeamCreate
     */
    private $teamCreateService;

    /**
     * @var Registration
     */
    private $userRegistrationService;

    /**
     * @param EntityManagerInterface $em
     * @param EventDispatcherInterface $ed
     */
    public function __construct(EntityManagerInterface $em, EventDispatcherInterface $ed, TeamCreate $teamCreateService, Registration $userRegistrationService)
    {
        $this->em = $em;
        $this->ed = $ed;
        $this->teamCreateService = $teamCreateService;
        $this->userRegistrationService = $userRegistrationService;
    }

    /**
     * @param $eventModel
     * @return bool
     */
    public function accepts($eventModel)
    {
        return ($eventModel instanceof CardSwipeModel);
    }

    /**
     * @param TableEvent $event
     */
    public function onEvent(TableEvent $event)
    {
        $tableEventModel = $event->getTableEventModel();

        if (!$this->accepts($tableEventModel)) {
            return;
        }
        
        $tableId = $tableEventModel->getTableId();
        $this->analyzeCardSwipe($tableEventModel, $tableId);
    }


    /**
     * @param CardSwipeModel $tableEventModel
     * @param $tableId
     * @return bool
     */
    private function analyzeCardSwipe(CardSwipeModel $tableEventModel, $tableId)
    {
        /* @var Game $gameEntity */
        /* @var Player $playerEntity */
        /* @var TableStatus $tableStatusEntity */
        /* @var CardSwipeModel $tableEventModel */


        $tableStatusEntity = $this->em->getRepository('IndigoGameBundle:TableStatus')->findOneBy(['tableId' => $tableId]);

        $teamPosition = $tableEventModel->getTeam();
        $playerPosition = $tableEventModel->getPlayer();
        $cardId = $tableEventModel->getCardId();
        //jei playerio nera, sukuriamas ir grazinamas jo Entity
        $playerEntity = $this->getPlayerEntityByCardId($cardId);
        printf(
            " * CardSwipe  cardid: %d, team/player position: %d/%d, PLAYER: %u [id: %d, ts:%d]\n",
            $cardId,
            $teamPosition,
            $playerPosition,
            $playerEntity->getId(),
            $tableEventModel->getId(),
            $tableEventModel->getTimeSec()
        );

        $gameEntity = $tableStatusEntity->getGame();


        if ($gameEntity) {

            if ($this->isDoubleSwipe($tableEventModel, $tableStatusEntity, $gameEntity)
                || $this->isScoreFixSwipe($tableEventModel, $gameEntity, $playerEntity)) {

                $this->setLastSwipe($tableEventModel, $tableStatusEntity);
                $this->em->flush();
                return false;
            }
        } else {

            $gameEntity  = $this->createGame($tableStatusEntity, $tableEventModel->getTimeSec());
        }

        $this->setLastSwipe($tableEventModel, $tableStatusEntity);
        $this->addPlayerToGame($gameEntity, $teamPosition, $playerPosition, $playerEntity);
        $tableStatusEntity->setBusy(TableStatusRepository::STATUS_BUSY);
        $this->em->flush();
    }

    /**
     * @param Player $playerEntity
     * @return Team
     */
    private function getPlayerSingleTeam(Player $playerEntity)
    {

        $teamEntity =  $this->em->getRepository('IndigoGameBundle:PlayerTeamRelation')->getPlayerSingleTeam($playerEntity);
        if (!$teamEntity) {

            $teamEntity =  $this->teamCreateService->createSinglePlayerTeam($playerEntity);
        }

        return $teamEntity;
    }



    /**
     * @param $tableStatusEntity
     * @return Game
     */
    private function createGame(TableStatus $tableStatusEntity, $startTs)
    {
        $gameEntity = new Game ();
        $dateTime = new \DateTime();
        $dateTime->setTimestamp($startTs);

        $gameEntity->setStartedAt($dateTime);
        $tableStatusEntity->setGame($gameEntity);
        $this->em->persist($tableStatusEntity);

        return $tableStatusEntity->getGame();
    }

    /**
     * @param $cardId
     * @return Player
     */
    private function createPlayer($cardId)
    {
        $playerEntity = new Player();
        $playerEntity->setCardId($cardId);
        $playerEntity->setUsername($cardId);
        $playerEntity->setEmail(sprintf('%s@%s', $playerEntity->getUsername(), Player::ANONYMOUS_EMAIL_DOMAIN));
        $playerEntity->setPassword(Player::ANONYMOUS_PASSWORD);
        $this->userRegistrationService->register($playerEntity);

        return $playerEntity;
    }

    /**
     * @param $tableEventModel
     * @param $tableStatusEntity
     * @return bool
     */
    private function isDoubleSwipe($tableEventModel, $tableStatusEntity, $gameEntity)
    {

        $timeBetweenSwipes = abs($tableEventModel->getTimeSec() - $tableStatusEntity->getLastSwipeTs());
        $result = false;
        if ((bool)($tableEventModel->getCardId() == $tableStatusEntity->getLastSwipedCardId()
            && $timeBetweenSwipes <= self::DOUBLE_SWIPE_IN
            && $timeBetweenSwipes >= self::DOUBLE_SWIPE_MIN_TS)) {


            if ($gameEntity && !$gameEntity->isGameStatusFinished()) {

                $event = new GameFinishEvent();
                $event->setGame($gameEntity);
                $event->setFinishTs($tableEventModel->getTimeSec());
                $this->ed->dispatch(GameEvents::GAME_FINISH_ON_DOUBLE_SWIPE, $event);
                printf("DoubleSwipe -> GAME FINISH'ed by team:%u, position: %u\n", $tableEventModel->getTeam(),
                    $tableEventModel->getPlayer());
            }
            $result = true;
        }

        return $result;
    }

    /**
     * @param $cardId
     * @return Player
     */
    private function getPlayerEntityByCardId($cardId)
    {
        $playerEntity = $this->em->getRepository('IndigoUserBundle:User')->findOneByCardId($cardId);
        if (!$playerEntity) {
            // automatiskai priregistruojam anonimini useri

            $playerEntity = $this->createPlayer($cardId);
        } elseif ($playerEntity->getTeams()->count() == 0) {

            $this->teamCreateService->createSinglePlayerTeam($playerEntity);
        }
        $this->em->flush();
        return $playerEntity;
    }

    /**
     * @param CardSwipeModel $tableEventModel
     * @param TableStatus $tableStatusEntity
     */
    private function setLastSwipe(CardSwipeModel $tableEventModel, TableStatus $tableStatusEntity)
    {

        $tableStatusEntity->setLastSwipedCardId($tableEventModel->getCardId());
        $tableStatusEntity->setLastSwipeTs($tableEventModel->getTimeSec());
        $this->em->persist($tableStatusEntity);
    }

    /**
     * @param CardSwipeModel $tableEventModel
     * @param $gameEntity
     * @param $playerEntity
     * @return bool
     */
    private function isScoreFixSwipe(CardSwipeModel $tableEventModel, $gameEntity, $playerEntity)
    {
        $result = false;
        if ($gameEntity->isPlayerInThisGame($playerEntity->getId()) === true) {

            if ($gameEntity->isGameStatusStarted()) {

                $gameEntity->addTeamScores($tableEventModel->getTeam(), -1);
                if ($gameEntity->getTeam0Score() == 0 && $gameEntity->getTeam1Score() == 0) {

                    $gameEntity->setStatus(GameStatusRepository::STATUS_GAME_WAITING);
                    print("Game status changed to WAITING\n");
                }
                printf("CardSwipe SCORE CHANGED to team:%u, position: %u, cardid: %u\n",
                    $tableEventModel->getTeam(), $tableEventModel->getPlayer(),
                    $tableEventModel->getCardId());
                $this->em->persist($gameEntity);
            } else {

                printf("CardSwipe IGNORED, game Started! team:%u, position: %u, cardid: %u\n",
                    $tableEventModel->getTeam(), $tableEventModel->getPlayer(),
                    $tableEventModel->getCardId());
            }
            $result = true;
        }

        return $result;
    }

    /**
     * @param Game $gameEntity
     * @param int $teamPosition
     * @param int $playerPosition
     * @param Player $playerEntity
     */
    private function addPlayerToGame(Game $gameEntity, $teamPosition, $playerPosition,Player $playerEntity)
    {
        if ($gameEntity->isGameStatusWaiting()) {

            $gameEntity->setPlayer($teamPosition, $playerPosition, $playerEntity);

            if ($gameEntity->getPlayersCountInTeam($teamPosition) == 2) {

                $teamEntity = $this->getCommonTeam($gameEntity, $teamPosition, $playerPosition, $playerEntity);
                $gameEntity->setTeam($teamPosition, $teamEntity);
            } else {

                $teamEntity = $this->getPlayerSingleTeam($playerEntity);
                $gameEntity->setTeam($teamPosition, $teamEntity);
            }

            $this->checkGameTimeReservation($gameEntity);
        }
    }

    /**
     * @param Game $gameEntity
     * @param $teamPosition
     * @param $playerPosition
     * @param Player $playerEntity
     * @return Team
     */
    private function getCommonTeam(Game $gameEntity, $teamPosition, $playerPosition, Player $playerEntity)
    {
        if ($playerPosition == 0) {

            $player0 = $playerEntity;
            $player1 = $gameEntity->getPlayer($teamPosition, 1);
        } else {

            $player0 = $gameEntity->getPlayer($teamPosition, 0);
            $player1 = $playerEntity;
        }
        $commonTeamId = $this->em->getRepository('IndigoGameBundle:PlayerTeamRelation')->getPlayersCommonTeam($player0,
            $player1);
        if (!$commonTeamId) {

            $teamEntity = $this->teamCreateService->createMultiPlayerTeam($player0, $player1);
            $this->em->flush();
            return $teamEntity;
        } else {

            $teamEntity = $this->em->getRepository('IndigoGameBundle:Team')->findOneById($commonTeamId);
            return $teamEntity;
        }
    }

    /**
     * @param Game $gameEntity
     * @return GameTime
     */
    private function checkGameTimeReservation(Game $gameEntity)
    {
        $contestEntity = null;
        $gameTimeEntity = $gameEntity->getGameTime();
        if ($gameTimeEntity == null) {
            $gameTimeEntity = $this->em->getRepository('IndigoGameBundle:GameTime')
                ->getEarliestReservation($gameEntity->getAllPlayers());
        }

        if ($gameTimeEntity != null) {

            $gameTimeEntity->setConfirmed(1);
            $gameEntity->setGameTime($gameTimeEntity);
            $this->persist($gameTimeEntity);
            $contestEntity = $gameTimeEntity->getContest();
        }

        if ($gameEntity->hasBothTeamsAllPlayers()) {

            $gameEntity->setStatus(GameStatusRepository::STATUS_GAME_READY);

            /** @var \Indigo\ContestBundle\Entity\Contest $contestEntity */
            if ($contestEntity && !$contestEntity->isContestOpen()) {

                $gameEntity->setMatchType(GameTypeRepository::TYPE_GAME_MATCH);
                $gameEntity->setContest($contestEntity);
                $this->em->persist($gameEntity);
            }
        }

    }

}
