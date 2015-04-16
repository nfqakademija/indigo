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

            printf ( "got AutoGoalEvent event: %d\n", $tableEventModel->getId());
            $this->analyzeAutoGoal($tableEventModel, $tableId);
        } elseif ($tableEventModel instanceof CardSwipeModel) {

            $this->analyzeCardSwipe($tableEventModel, $tableId);
        }
    }

    private function analyzeAutoGoal(TableActionInterface $tableEventModel, $tableId)
    {
        /** @var AutoGoalModel $tableEventModel */
        /** @var Game $gameEntity */
        /** @var TableStatus $tableStatusEntity */

        $tableStatusEntity = $this->em
                                ->getRepository('IndigoGameBundle:TableStatus')
                                ->findOneByTableId((int)$tableId);
        if ($tableStatusEntity) {

            $gameEntity = $this->em
                            ->getRepository('IndigoGameBundle:Game')
                            ->findOneByTableStatusId($tableStatusEntity->getId());

            if ($gameEntity && $gameEntity->getStatus() == GameStatusRepository::STATUS_GAME_READY) { // arba game_started

                $teamPosition = $tableEventModel->getTeam();
                $gameEntity->addTeamScore($teamPosition);
                $this->em->persist($gameEntity);
                if ($gameEntity->getTeamScore($teamPosition) >= 10) {

                    $event = new GameFinishEvent();
                    $event->setGame($gameEntity);
                    $event->setTableStatus($tableStatusEntity);
                    $this->ed->dispatch(GameEvents::GAME_FINISH_ON_SCORE, $event);
                }
                $this->em->flush();
            }
        }
    }

    /**
     * @param TableActionInterface $tableEventModel
     * @param $tableId
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

        $playerEntity = $this->em->getRepository('IndigoUserBundle:User')->findOneByCardId($cardId);
        /** @var QueryBuilder $qb */
//        $playerEntity = $this->em->getRepository('IndigoUserBundle:User')->getPlayerWithTeamsByCardId($cardId);

        if (!$playerEntity) { // automatiskai priregistruojam anonimini useri
            $playerEntity = $this->createPlayer($cardId);
            $teamEntity = $this->createSinglePlayerTeam($playerEntity);
        } elseif ($playerEntity->getTeams()->count() == 0) {
            $teamEntity = $this->createSinglePlayerTeam($playerEntity);
        }

        $playerId = $playerEntity->getId();

        if ($tableId) {
            $gameEntity = $this->em->getRepository('IndigoGameBundle:Game')->findOneByTableStatusId($tableStatusEntity->getId());
            //$gameEntity = $tableStatusEntity->getGame();
        }

        if ($this->isDoubleSwipe($tableEventModel, $tableStatusEntity)) {

            if ($tableStatusEntity->getGames()->getId() > 0) {

                $tableStatusEntity->setLastSwipedCardId(0);
                $tableStatusEntity->setLastSwipeTs(0);
                $this->em->persist($tableStatusEntity);

                if ($gameEntity && $gameEntity->getStatus() != GameStatusRepository::STATUS_GAME_FINISHED) {

                    $event = new GameFinishEvent();
                    $event->setGame($gameEntity);
                    $this->ed->dispatch(GameEvents::GAME_FINISH, $event);
                }

                $this->em->flush();
            }

            return true;
        }

            //$tableStatusEntity->setLastSwipedCardId($tableEventModel->getCardId());
            // TEST!
            $tableStatusEntity->setLastSwipedCardId(0);
            $tableStatusEntity->setLastSwipeTs($tableEventModel->getTimeSec());
            $this->em->persist($tableStatusEntity);
            $this->em->flush();


            if ($gameEntity) {

                if ($gameEntity->isPlayerInThisGame($playerId) === true) {

                    // ignore pakartotini cardswipe, jei jis jau "prisidaves", galbut bande double swipint, bet per letai..
                    return true;
                } else {

                    if ($gameEntity->getPlayersCountInTeam($teamPosition) == 2) {

                        if (! $this->isGameStartedOrFinished($gameEntity)) {

                            $gameEntity->setPlayer($teamPosition, $playerPosition, $playerEntity);
                        } else {

                            // zaidimas pradetas ar pasibaiges, trecias zaidejas nepriimtas :)
                            return false;
                        }
                    } else {

                        // yra laisvos vietos teame
                        $gameEntity->setPlayer($teamPosition, $playerPosition, $playerEntity);
                    }
                    //if ($gameEntity->getPlayersCountInTeam($teamPosition) == 2) {
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
                                $commonTeamId = $teamEntity->getId();
                            }
                            $gameEntity->setTeamId($teamPosition, $commonTeamId);

/*                            $player0Teams = $player0->getTeams();
                            $player1Teams = $player1->getTeams();

                            foreach($player0Teams as $p0team) {
                                $c = new Criteria();
                                $c->where($c->expr()->eq('id' ,  $p0team->getTeamId()));

                                if (($match = $player1Teams->matching($c))) {
                                    break;
                                }
                            }

                            $commonTeam = new ArrayCollection (array_intersect((array) $player0Teams, (array) $player1Teams));
*/


                            if ($gameEntity->isBothTeamReady()) {
                                //TODO: is reserved?
                                $gameEntity->setStatus(GameStatusRepository::STATUS_GAME_READY);
                            }
                    }


                }
            } else { // nesukurtas Game'as

                $gameEntity  = $this->createGame($tableStatusEntity);
                //$tableStatusEntity->setGameId($gameEntity->getId());
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
        if ($gameStatus === null) {

            $gameStatus = GameStatusRepository::STATUS_GAME_WAITING;
        }
        $gameTimeEntity = new GameTime();
        $this->em->persist($gameTimeEntity);
        $this->em->flush();

        $gameEntity = new Game ();
        $gameEntity->setGameTime($gameTimeEntity);
        $gameEntity->setTableStatus($tableStatusEntity);
        $gameEntity->setStatus($gameStatus);
        $this->em->persist($gameEntity);
        $this->em->flush($gameEntity);
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
        $this->em->flush();
        $this->createPlayerTeamRelation($player0, $teamEntity);
        $this->createPlayerTeamRelation($player1, $teamEntity);

        $this->em->flush();

        return $teamEntity;
    }

    /**
     * @param Player $player
     * @param string $name
     * @return Team
     */
    private function createSinglePlayerTeam(Player $player, $name='SingleTeam') {

        $teamEntity = $this->createTeam(true, $name);
        $this->createPlayerTeamRelation($player, $teamEntity);

        $this->em->persist($teamEntity);

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
        $playerTeamRelationEntity->setPlayer($player);
        $playerTeamRelationEntity->setTeam($team);
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
            $timeBetweenSwipes <= self::DOUBLE_SWIPE &&
            $timeBetweenSwipes >= self::DOUBLE_SWIPE_MIN_TS);
    }

    private function isGameStartedOrFinished(Game $game) {
        return (
            ($game->getStatus() == GameStatusRepository::STATUS_GAME_STARTED) ||
            ($game->getStatus() == GameStatusRepository::STATUS_GAME_FINISHED)
        );
    }

}