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
use Indigo\TableBundle\Model\TableActionInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Indigo\TableBundle\Model\AutoGoalModel;


class AutoGoalListener
{
    const MAX_SCORES = 10;
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
                    // TODO: pakeist i 10
                    if ($gameEntity->getTeamScore($teamPosition) >= $gameEntity->getContest()->getScoreLimit()) {

                        if ($teamWon = $gameEntity->getTeam($teamPosition)) {

                            $gameEntity->setTeamWon($teamWon);
                        }
                        $event = new GameFinishEvent();
                        $event->setGame($gameEntity);
                        $event->setTableStatus($tableStatusEntity);
                        $this->ed->dispatch(GameEvents::GAME_FINISH_ON_SCORE, $event);
                        printf("-------------------- dublicate GAME(on scoreFINISH: %u) ----------------- \n", $gameEntity->getContest()->getScoreLimit());

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
                        if ($gameEntity->getTeam0()) {
                            $newGameEntity->setTeam0($gameEntity->getTeam0());
                        }
                        if ($gameEntity->getTeam1()) {
                            $newGameEntity->setTeam1($gameEntity->getTeam1());
                        }

                        $newGameEntity->setTableStatus($gameEntity->getTableStatus());
                        $newGameEntity->setContest($gameEntity->getContest());

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

}