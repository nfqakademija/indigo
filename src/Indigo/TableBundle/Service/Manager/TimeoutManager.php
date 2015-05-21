<?php

namespace Indigo\TableBundle\Service\Manager;

use Doctrine\ORM\EntityManagerInterface;
use Indigo\GameBundle\Entity\TableStatus;
use Indigo\GameBundle\Entity\TableStatusRepository;
use Indigo\GameBundle\Event\GameEvents;
use Indigo\GameBundle\Event\GameFinishEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class TimeoutManager
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
     * @param integer $tableKey
     */
    public function checkBetweenResponses($tableKey)
    {
        if ($tableStatusEntity = $this->getTableStatus($tableKey)) {

            if ($tableStatusEntity->hasTimeout()) {

                $this->handleTimeout($tableStatusEntity);
            }
        }
    }

    /**
     * @param $tableStatusEntity
     */
    public function handleTimeout(TableStatus $tableStatusEntity)
    {
        if ($tableStatusEntity->getBusy() != TableStatusRepository::STATUS_IDLE) {

            printf ("Timeout has occured! table_id: %d\n", $tableStatusEntity->getTableId());
            $tableStatusEntity->setBusy(TableStatusRepository::STATUS_IDLE);
        }
        if ($gameEntity = $tableStatusEntity->getGame()) {

            $event = new GameFinishEvent();
            $event->setGame($gameEntity);
            $event->setTableStatus($tableStatusEntity);
            $this->ed->dispatch(GameEvents::GAME_FINISH_TIMEOUT, $event);
        } else {

            $this->em->persist($tableStatusEntity);
            $this->em->flush();
        }
    }

    private function getTableStatus($tableKey)
    {
        return  $this->em->getRepository('IndigoGameBundle:TableStatus')->findOneBy(
            [
                'tableId' => $tableKey
            ]
        );
    }
}