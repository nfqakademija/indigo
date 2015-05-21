<?php

namespace Indigo\TableBundle\EventListener;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Indigo\GameBundle\Entity\Game;
use Indigo\GameBundle\Entity\TableStatus;
use Indigo\GameBundle\Event\GameEvents;
use Indigo\GameBundle\Event\GameFinishEvent;
use Indigo\GameBundle\Repository\GameStatusRepository;
use Indigo\TableBundle\Event\TableEvent;
use Indigo\TableBundle\Model\AutoGoalModel;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class AutoGoalListener
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var EventDispatcherInterface
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
        return ($eventModel instanceof AutoGoalModel);
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
        printf ( "AutoGoal!!!! teamScores: %u [id: %d, ts:%d, ]\n",
            $tableEventModel->getTeam(),
            $tableEventModel->getId(),
            $tableEventModel->getTimeSec()
        );
        $this->analyzeAutoGoal($tableEventModel, $tableEventModel->getTableId());

    }

    /**
     * @param AutoGoalModel $tableEventModel
     * @param int $tableId
     */
    private function analyzeAutoGoal(AutoGoalModel $tableEventModel, $tableId)
    {
        /** @var AutoGoalModel $tableEventModel */
        /** @var Game $gameEntity */
        $tableStatusEntity = $this->getTableStatus($tableId);
        if ($gameEntity = $this->getGame($tableStatusEntity)) {

            $teamPosition = $tableEventModel->getTeam();

            /**
             * Game turim, vadinas turim ir playeriu
             */
            if (!$gameEntity->isGameStatusFinished()) {

                if (!$gameEntity->isGameStatusStarted()) {

                    $gameEntity->setStatus(GameStatusRepository::STATUS_GAME_STARTED);
                }

                $gameEntity->addTeamScore($teamPosition);
                if ($gameEntity->getTeamScore($teamPosition) >= $gameEntity->getContest()->getScoreLimit()) {

                    if ($teamWon = $gameEntity->getTeam($teamPosition)) {

                        $gameEntity->setTeamWon($teamWon);
                    }

                    $event = new GameFinishEvent();
                    $event->setGame($gameEntity);
                    $event->setTableStatus($tableStatusEntity);
                    $event->setFinishTs($tableEventModel->getTimeSec());

                    $this->ed->dispatch(GameEvents::GAME_FINISH_ON_SCORE, $event);

                    $tableStatusEntity->addNewGame($this->dublicateGame($gameEntity));
                    $this->em->persist($tableStatusEntity);

                }
                $this->em->flush();
            }

        }

    }

    /**
     * @param $tableId
     * @return mixed
     */
    private function getTableStatus($tableId)
    {
        $tableStatusEntity = $this->em
            ->getRepository('IndigoGameBundle:TableStatus')
            ->findOneByTableId((int)$tableId);

        return $tableStatusEntity;
    }

    /**
     * @param $tableStatusEntity
     * @return mixed|void
     */
    private function getGame($tableStatusEntity)
    {
        if ($tableStatusEntity instanceof TableStatus) {

            return $tableStatusEntity->getGame();
        }

        return;
    }

    /**
     * @param $gameEntity
     * @return Game
     */
    private function dublicateGame(Game $gameEntity)
    {
        printf("-------------------- dublicate GAME(on scoreFINISH: %u) ----------------- \n", $gameEntity->getContest()->getScoreLimit());

        $newGameEntity = new Game();
        if ($gameEntity->getTeam0()) {

            $gameEntity->getTeam0Player0Id() && $newGameEntity->setTeam0Player0Id($gameEntity->getTeam0Player0Id());
            $gameEntity->getTeam0Player1Id() && $newGameEntity->setTeam0Player1Id($gameEntity->getTeam0Player1Id());
            $newGameEntity->setTeam0($gameEntity->getTeam0());
        }
        if ($gameEntity->getTeam1()) {

            $gameEntity->getTeam1Player0Id() && $newGameEntity->setTeam1Player0Id($gameEntity->getTeam1Player0Id());
            $gameEntity->getTeam1Player1Id() && $newGameEntity->setTeam1Player1Id($gameEntity->getTeam1Player1Id());
            $newGameEntity->setTeam1($gameEntity->getTeam1());
        }

        $newGameEntity->setTableStatus($gameEntity->getTableStatus());
        $newGameEntity->setContest($gameEntity->getContest());
        $newGameEntity->setMatchType($gameEntity->getMatchType());
        $newGameEntity->setGameTime($gameEntity->getGameTime());
        $newGameEntity->setStatus(GameStatusRepository::STATUS_GAME_STARTED);
        $newGameEntity->setStartedAt($gameEntity->getFinishedAt());
        $this->em->persist($newGameEntity);

        return $newGameEntity;
    }
}
