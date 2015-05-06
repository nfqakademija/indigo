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


    public function __construct(EntityManagerInterface $em, EventDispatcherInterface $ed)
    {
        $this->em = $em;
        $this->ed = $ed;
    }


    /**
     * @param TableStatus $tableStatusEntity
     */
    public function check(TableStatus $tableStatusEntity)
    {
            /** @var TableStatus $tableStatusEntity */
            if ($tableStatusEntity->hasTimeout())  {

                $tableStatusEntity->setBusy(TableStatusRepository::STATUS_IDLE);
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
    }
}