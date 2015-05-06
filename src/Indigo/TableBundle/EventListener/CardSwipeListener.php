<?php

namespace Indigo\TableBundle\EventListener;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Indigo\ContestBundle\IndigoContestBundle;
use Indigo\GameBundle\Entity\Game;
use Indigo\GameBundle\Entity\GameTime;
use Indigo\GameBundle\Entity\PlayerTeamRelation;
use Indigo\GameBundle\Entity\TableStatus;
use Indigo\GameBundle\Entity\Team;
use Indigo\GameBundle\Event\GameEvents;
use Indigo\GameBundle\Event\GameFinishEvent;
use Indigo\GameBundle\Repository\GameStatusRepository;
use Indigo\GameBundle\Repository\GameTypeRepository;
use Indigo\TableBundle\Event\TableEvent;
use Indigo\TableBundle\Model\CardSwipeModel;
use Indigo\TableBundle\Model\TableActionInterface;
use Indigo\GameBundle\Entity\TableStatusRepository;
use Indigo\UserBundle\Entity\User as Player;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class CardSwipeListener
{
    const DOUBLE_SWIPE_IN = 5;
    const DOUBLE_SWIPE_MIN_TS = 1;
    const ANONYMOUS_EMAIL_DOMAIN = 'example.com';
    const ANONYMOUS_USERNAME = 'anonymous';
    const ANONYMOUS_PASSWORD = 'incredibly';
    const SINGLE_PLAYER_TEAM_NAME = 'singlePlayerTeam';
    const MULTI_PLAYER_TEAM_NAME = 'multiPlayerTeam';

    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var EventDispatcher
     */
    private $ed;

    /**
     * @param EntityManagerInterface $em
     * @param EventDispatcherInterface $ed
     */
    public function __construct(EntityManagerInterface $em, EventDispatcherInterface $ed)
    {
        $this->em = $em;
        $this->ed = $ed;
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
     * @param TableActionInterface $tableEventModel
     * @param $tableId
     * @return bool
     */
    private function analyzeCardSwipe(TableActionInterface $tableEventModel, $tableId)
    {
        /* @var Game $gameEntity */
        /* @var Player $playerEntity */
        /* @var TableStatus $tableStatusEntity */
        /* @var CardSwipeModel $tableEventModel */
        $gameEntity = null;
        $tableStatusEntity = $this->em->getRepository('IndigoGameBundle:TableStatus')->findOneByTableId($tableId);

        $tableId = $tableEventModel->getTableId();
        $teamPosition = $tableEventModel->getTeam();
        $playerPosition = $tableEventModel->getPlayer();
        $cardId = $tableEventModel->getCardId();
        //jei playerio nera, sukuriamas ir grazinamas jo Entity
        $playerEntity = $this->getPlayerEntityByCardId($cardId);
        printf(
            " * CardSwipe  cardid: %d, team/player position: %d/%d, PLAYER: %u \n",
            $cardId,
            $teamPosition,
            $playerPosition,
            $playerEntity->getId()
        );


        if ($tableId) {

            $gameEntity = $tableStatusEntity->getGame();
/*            (if (!method_exists($gameEntity,'getTableStatus')) {

                $tableStatusEntity->setGame(null);
            }*/

            //$gameEntity = $tableStatusEntity->getActiveGame();
        }

        if ($this->isDoubleSwipe($tableEventModel, $tableStatusEntity)) {

            if ($gameEntity && !$gameEntity->isGameStatusFinished()) {

                $event = new GameFinishEvent();
                $event->setGame($gameEntity);
                $this->ed->dispatch(GameEvents::GAME_FINISH_ON_DOUBLE_SWIPE, $event);
                printf("DoubleSwipe -> GAME FINISH'ed by team:%u, position: %u\n", $tableEventModel->getTeam(), $tableEventModel->getPlayer());
            }
            return true;
        }

        $this->setLastSwipe($tableEventModel, $tableStatusEntity);


            if (!$gameEntity) {

                $gameEntity  = $this->createGame($tableStatusEntity);
                $this->em->flush();
            } else {

                if ($gameEntity->isGameStatusStarted()) {

                    printf("CardSwipe IGNORED, game Started! team:%u, position: %u, cardid: %u\n", $tableEventModel->getTeam(), $tableEventModel->getPlayer(), $tableEventModel->getCardId());

                    return false;
                }
            }

            if ($gameEntity->isPlayerInThisGame($playerEntity->getId()) === true) {

                // ignore pakartotini cardswipe, jei jis jau "prisidaves", galbut bande double swipint, bet per letai..
                printf("CardSwipe IGNORED,  player already in game! team:%u, position: %u, cardid: %u\n", $tableEventModel->getTeam(), $tableEventModel->getPlayer(), $tableEventModel->getCardId());
                return true;
            } else {

               $gameEntity->setPlayer($teamPosition, $playerPosition, $playerEntity);

                if ($gameEntity->getPlayersCountInTeam($teamPosition) == 2) {

                        //check teams and setTeam!
                        // kad nereiktu papildomai nukraudinet userio, kuri jau zinau ...
                        if ($playerPosition == 0) {

                            $player0 = $playerEntity;
                            $player1 = $gameEntity->getPlayer($teamPosition, 1);
                        } else {

                            $player0 = $gameEntity->getPlayer($teamPosition, 0);
                            $player1 = $playerEntity;
                        }
                        $commonTeamId = $this->em->getRepository('IndigoGameBundle:PlayerTeamRelation')->getPlayersCommonTeam($player0, $player1);
                        if (!$commonTeamId) {

                            $teamEntity = $this->createMultiPlayerTeam($player0, $player1, self::MULTI_PLAYER_TEAM_NAME);
                            $this->em->flush();
                        } else {

                            $teamEntity = $this->em->getRepository('IndigoGameBundle:Team')->findOneById($commonTeamId);
                        }

                        $gameEntity->setTeam($teamPosition, $teamEntity);

                        if ($gameEntity->hasBothTeamsAllPlayers()) {

                            $gameEntity->setStatus(GameStatusRepository::STATUS_GAME_READY);

                            $gameTimeEntity = $gameEntity->getGameTime();
                            if ($gameTimeEntity !== null) {

                                $contestEntity = $gameTimeEntity->getContest();
                                /** @var \Indigo\ContestBundle\Entity\Contest $contestEntity */
                                if (!$contestEntity->isContestOpen()) {

                                    $gameEntity->setMatchType(GameTypeRepository::TYPE_GAME_MATCH);
                                    $gameEntity->setContest($contestEntity);
                                    $this->em->persist($gameEntity);
                                }
                            }
                        }
                } else {

                    $teamEntity = $this->getPlayerSingleTeam($playerEntity);
                    $gameEntity->setTeam($teamPosition, $teamEntity);
                }

                if (!$gameEntity->getGameTime()) {

                    $gameTimeEntity = $this->em->getRepository('IndigoGameBundle:GameTime')
                        ->getEarliestReservation($gameEntity->getAllPlayers());
                    /** @var GameTime $gameTimeEntity */
                    if ($gameTimeEntity) {

                        $gameTimeEntity->setConfirmed(1);
                        $gameEntity->setGameTime($gameTimeEntity);

                        $this->em->persist($gameEntity);
                    } // jei nera rezervuoto laiko, tai ir paliekam NULL
                }
            }


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
            $teamEntity  = $this->createSinglePlayerTeam($playerEntity);
        }

        return $teamEntity;
    }



    /**
     * @param $tableStatusEntity
     * @return Game
     */
    private function createGame(TableStatus $tableStatusEntity)
    {

        //$gameEntity->setTableStatus($tableStatusEntity);
        //$gameEntity->setGameTime(new GameTime());
        //$this->em->persist($gameEntity);

        $tableStatusEntity->setGame(new Game ());
        $this->em->persist($tableStatusEntity);
        $this->em->flush();
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
        $playerEntity->setUsername(sprintf('%s%d', self::ANONYMOUS_USERNAME, $cardId));
        $playerEntity->setEmail(sprintf('%s@%s', $playerEntity->getUsername(), self::ANONYMOUS_EMAIL_DOMAIN));
        $playerEntity->setPassword(self::ANONYMOUS_PASSWORD);
        $this->em->persist($playerEntity);
        $this->em->flush($playerEntity);

        return $playerEntity;
    }

    /**
     * @param string $name
     * @return Team
     */
    private function createMultiPlayerTeam(Player $player0, Player $player1, $name = "Broliai Aliukai") {

        $teamEntity = $this->createTeam(false, $name);

        $playerToTeamRelation1 = $this->createPlayerTeamRelation($player0, $teamEntity);
        $playerToTeamRelation2 = $this->createPlayerTeamRelation($player1, $teamEntity);
        $this->em->persist($playerToTeamRelation1);
        $this->em->persist($playerToTeamRelation2);

        return $teamEntity;
    }

    /**
     * @param Player $player
     * @param string $name
     * @return Team
     */
    private function createSinglePlayerTeam(Player $player, $name='SingleTeam')
    {
        $teamEntity = $this->createTeam(true, $name);
        $playerToTeamRelation = $this->createPlayerTeamRelation($player, $teamEntity);
        $this->em->persist($playerToTeamRelation);

        return $teamEntity;
    }

    /**
     * @param $single
     * @param $name
     * @return Team
     */
    private function createTeam($single, $name)
    {
        $teamEntity = new Team();
        $teamEntity->setIsSingle((bool) $single);
        $teamEntity->setName($name);

        return $teamEntity;
    }

    /**
     * @param Player $player
     * @param Team $team
     * @return PlayerTeamRelation
     */
    private function createPlayerTeamRelation(Player $player, Team $team)
    {
        $playerTeamRelationEntity = new PlayerTeamRelation();
        $playerTeamRelationEntity
            ->setPlayer($player)
            ->setTeam($team);

        return $playerTeamRelationEntity;
    }

    /**
     * @param $tableEventModel
     * @param $tableStatusEntity
     * @return bool
     */
    private function isDoubleSwipe($tableEventModel, $tableStatusEntity)
    {
        $timeBetweenSwipes = abs($tableEventModel->getTimeSec() - $tableStatusEntity->getLastSwipeTs());
        return (bool) ($tableEventModel->getCardId() == $tableStatusEntity->getLastSwipedCardId() &&
            $timeBetweenSwipes <= self::DOUBLE_SWIPE_IN &&
            $timeBetweenSwipes >= self::DOUBLE_SWIPE_MIN_TS);
    }

    /**
     * @param Game $game
     * @return bool
     */
    private function isGameStartedOrFinished(Game $game) {
        return (
            ($game->getStatus() == GameStatusRepository::STATUS_GAME_STARTED) ||
            ($game->getStatus() == GameStatusRepository::STATUS_GAME_FINISHED)
        );
    }

    /**
     * @param $cardId
     * @return array
     */
    private function getPlayerEntityByCardId($cardId)
    {
        $playerEntity = $this->em->getRepository('IndigoUserBundle:User')->findOneByCardId($cardId);
        if (!$playerEntity) {
            // automatiskai priregistruojam anonimini useri

            $playerEntity = $this->createPlayer($cardId);
            $this->createSinglePlayerTeam($playerEntity, self::SINGLE_PLAYER_TEAM_NAME);
            $this->em->flush();
        } elseif ($playerEntity->getTeams()->count() == 0) {

            $this->createSinglePlayerTeam($playerEntity, self::SINGLE_PLAYER_TEAM_NAME);
            $this->em->flush();
        }
        return $playerEntity;
    }

    /**
     * @param TableActionInterface $tableEventModel
     * @param $tableStatusEntity
     */
    private function setLastSwipe(TableActionInterface $tableEventModel, $tableStatusEntity)
    {
        $tableStatusEntity->setLastSwipedCardId($tableEventModel->getCardId());
        $tableStatusEntity->setLastSwipeTs($tableEventModel->getTimeSec());
        $this->em->persist($tableStatusEntity);
        $this->em->flush();
    }


}