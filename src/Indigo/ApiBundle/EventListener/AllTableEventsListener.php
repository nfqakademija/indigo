<?php

namespace Indigo\ApiBundle\EventListener;

use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Indigo\ApiBundle\Event\TableEvent;
use Indigo\ApiBundle\Model\AutoGoalModel;
use Indigo\ApiBundle\Model\CardSwipeModel;
use Indigo\ApiBundle\Model\TableActionInterface;
use Indigo\ApiBundle\Model\TableShakeModel;
use Doctrine\ORM\EntityManager;
use Indigo\ApiBundle\Repository\TableStatusRepository;
use Indigo\GameBundle\Entity\Game;
use Indigo\GameBundle\Entity\GameTime;
use Indigo\GameBundle\Entity\TableStatus;
use Indigo\GameBundle\Entity\Team;
use Indigo\GameBundle\Entity\GameTimeRepository;
use Indigo\GameBundle\Entity\PlayerTeamRelation;
use Indigo\GameBundle\Event\GameFinishEvent;
use Indigo\GameBundle\Repository\GameStatusRepository;
use Indigo\GameBundle\Repository\GameTypeRepository;
use Indigo\UserBundle\Entity\User as Player;
use Indigo\GameBundle\Event\GameEvents;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class AllTableEventsListener
{

    const DOUBLE_SWIPE_IN = 5;
    const DOUBLE_SWIPE_MIN_TS = 1;

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
        return ($eventModel instanceof AutoGoalModelModel);
    }

    /**
     * @param TableEvent $event
     */
    public function onNewEvent(TableEvent $event)
    {
        $tableEventModel = $event->getTableEventModel();

//        if (!$this->accepts($tableEventModel)) {
//            return;
//        }
        
        $tableId = $tableEventModel->getTableId();

        if ($tableEventModel instanceof TableShakeModel) {
            printf ("got tableShake event: %d\n", $tableEventModel->getId());
            // pasizymet last shake ts
            $tableStatusEntity = $this->em->getRepository('IndigoGameBundle:TableStatus')
                ->findOneById($tableId);
            if ($tableStatusEntity) {

                $tableStatusEntity->setLastTableShakeTs($tableEventModel->getTimeSec());
                $this->em->persist($tableStatusEntity);
                $this->em->flush();
            }
        }  elseif ($tableEventModel instanceof AutoGoalModel) {

            printf ( "got AutoGoalEvent event: %d, teamScores: %u\n", $tableEventModel->getId(), $tableEventModel->getTeam());
            $this->analyzeAutoGoal($tableEventModel, $tableId);
        } elseif ($tableEventModel instanceof CardSwipeModel) {

            $this->analyzeCardSwipe($tableEventModel, $tableId);
        }
    }

    /**
     * @param TableActionInterface $tableEventModel
     * @param $tableId
     */
    private function analyzeAutoGoal(TableActionInterface $tableEventModel, $tableId)
    {
        /** @var AutoGoalModel $tableEventModel */
        /** @var Game $gameEntity */
        /** @var TableStatus $tableStatusEntity */

        $tableStatusEntity = $this->em
                                ->getRepository('IndigoGameBundle:TableStatus')
                                ->findOneByTableId((int)$tableId);
        if ($tableStatusEntity) {

            $gameEntity = $tableStatusEntity->getGame();

            if ($gameEntity) {

                $teamPosition = $tableEventModel->getTeam();

                /**
                 * Game turim, vadinas turim ir playeriu
                 */
                if (!$gameEntity->isGameStatusFinished()) {

                    if (!$gameEntity->isGameStatusStarted()) {

                        $gameEntity->setStatus(GameStatusRepository::STATUS_GAME_STARTED);
                    }

                    $gameEntity->addTeamScore($teamPosition);
                    if ($gameEntity->getTeamScore($teamPosition) >= 10) {

                        $event = new GameFinishEvent();
                        $event->setGame($gameEntity);
                        $event->setTableStatus($tableStatusEntity);
                        $this->ed->dispatch(GameEvents::GAME_FINISH_ON_SCORE, $event);


                        $newGameEntity = new Game();
                        if ($gameEntity->getTeam0Player0Id()) {
                            $newGameEntity->setTeam0Player0Id($gameEntity->getTeam0Player0Id());
                        }
                        if ($gameEntity->getTeam0Player1Id()) {
                            $newGameEntity->setTeam0Player1Id($gameEntity->getTeam0Player1Id());
                        }
                        if ($gameEntity->getTeam1Player0Id()) {
                            $newGameEntity->setTeam1Player0Id($gameEntity->getTeam1Player0Id());
                        }
                        if ($gameEntity->getTeam1Player1Id()) {
                            $newGameEntity->setTeam1Player1Id($gameEntity->getTeam1Player1Id());
                        }
                        $newGameEntity->setTeam0Id($gameEntity->getTeam0Id());
                        $newGameEntity->setTeam1Id($gameEntity->getTeam1Id());

                        $newGameEntity->setTableStatus($gameEntity->getTableStatus());
                        $newGameEntity->setMatchType($gameEntity->getMatchType());

                        $newGameEntity->setGameTime($gameEntity->getGameTime());

                        // darom STARTED, nes ant READY jei kas noretu double swipintis - is kart prijungines ji i komanda su kitu pries tai zaidziusiu.
                        $newGameEntity->setStatus(GameStatusRepository::STATUS_GAME_STARTED);
                        $tableStatusEntity->addNewGame($newGameEntity);

                        $this->em->persist($newGameEntity);
                        $this->em->persist($tableStatusEntity);

                    }
                }
                $this->em->flush();
            }
        }
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

        $gameEntity = null;
        $tableStatusEntity = $this->em->getRepository('IndigoGameBundle:TableStatus')->findOneByTableId($tableId);

        $tableId = $tableEventModel->getTableId();
        $teamPosition = $tableEventModel->getTeam();
        $playerPosition = $tableEventModel->getPlayer();
        $cardId = $tableEventModel->getCardId();

        printf(
            "%s got CardSwipe event: %d, cardid: %d, team/player position: %d/%d \n",
            date('Y-m-d H:i:s', $tableEventModel->getTimeSec()),
            $tableEventModel->getId(),
            $cardId,
            $teamPosition,
            $playerPosition
        );

        //jei playerio nera, sukuriamas ir grazinamas jo Entity
        $playerEntity = $this->getPlayerEntityByCardId($cardId);
        printf("player id : %u\n", $playerEntity->getId());
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
            }
            return true;
        }

        $this->setLastSwipe($tableEventModel, $tableStatusEntity);


            if (!$gameEntity) {
                $gameEntity  = $this->createGame($tableStatusEntity);
            } else {
                if ($gameEntity->isGameStatusStarted()) {

                    return false;
                }
            }

            if ($gameEntity->isPlayerInThisGame($playerEntity->getId()) === true) {

                // ignore pakartotini cardswipe, jei jis jau "prisidaves", galbut bande double swipint, bet per letai..
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

                            $teamEntity = $this->createMultiPlayerTeam($player0, $player1);
                            $this->em->flush();
                            $commonTeamId = $teamEntity->getId();
                        }
                        $gameEntity->setTeamId($teamPosition, $commonTeamId);

                        if ($gameEntity->isBothTeamReady()) {
                            //if ($gameEntity->getGameTime()->getTimeOwner()) {
                            $gameEntity->setStatus(GameStatusRepository::STATUS_GAME_READY);
                            //TODO: is reserved?
                            if (!$gameEntity->getGameTime()) {


                                $gameTimeEntity = $this->em->getRepository('IndigoGameBundle:GameTime')
                                    ->getEarliestReservation($gameEntity->getAllPlayers());

                                if ($gameTimeEntity) {
                                    $gameTimeEntity->setConfirmed(1);
                                    $gameEntity->setMatchType(GameTypeRepository::TYPE_GAME_MATCH);
                                } else {

                                    $gameTimeEntity = new GameTime();

                                    //TODO 15min langa daryti?
                                }

                                $gameEntity->setGameTime($gameTimeEntity);

                                $this->em->persist($gameTimeEntity);
                            }
                        }
                }
            }


            $tableStatusEntity->setBusy(TableStatusRepository::STATUS_BUSY);
            $this->em->flush();


    }

    /**
     * @param $tableStatusEntity
     * @return Game
     */
    private function createGame(TableStatus $tableStatusEntity, $gameStatus = null)
    {

        $gameEntity = new Game ();
        $gameEntity->setGameTime(new GameTime());

        if ($gameStatus !== null) {

            $gameEntity->setStatus($gameStatus);
        }

        $this->em->persist($gameEntity);

        $tableStatusEntity->addNewGame($gameEntity);

        $this->em->persist($tableStatusEntity);
        $this->em->persist($gameEntity);

        return $gameEntity;
    }

    /**
     * @param $cardId
     * @return Player
     */
    private function createPlayer($cardId)
    {
        $playerEntity = new Player();
        $playerEntity->setCardId($cardId);
        $playerEntity->setUsername(sprintf('anonymous%u', $cardId));
        $playerEntity->setEmail(sprintf('%s@example.com', $playerEntity->getUsername()));
        $playerEntity->setPassword('incredibly');
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
            $this->createSinglePlayerTeam($playerEntity);
            $this->em->flush();
        } elseif ($playerEntity->getTeams()->count() == 0) {

            $this->createSinglePlayerTeam($playerEntity);
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