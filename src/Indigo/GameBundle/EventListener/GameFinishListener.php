<?php

namespace Indigo\GameBundle\EventListener;

use Doctrine\ORM\EntityManagerInterface;
use Indigo\GameBundle\Event\GameFinishEvent;
use Indigo\GameBundle\Repository\GameStatusRepository;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class GameFinishListener {

    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param GameFinishEvent $event
     */
    public function onGameFinishDoubleSwipe(GameFinishEvent $event)
    {
        $gameEntity = $this->closeGame($event);
        $gameEntity->getTableStatus()->setLastSwipedCardId(0);
        $gameEntity->getTableStatus()->setLastSwipeTs(0);
        $this->em->persist($gameEntity);

        $this->em->flush();
    }

    /**
     * @param GameFinishEvent $event
     */
    public function onGameFinishMaxScoreReached(GameFinishEvent $event)
    {
        $gameEntity = $this->closeGame($event);
        $this->em->persist($gameEntity);

        $this->em->flush();
    }

    /**
     * @param GameFinishEvent $event
     */
    public function onGameFinishTimeout(GameFinishEvent $event)
    {
        $gameEntity = $this->closeGame($event);
        $this->em->persist($gameEntity);

        $this->em->flush();
    }

    /**
     * @param GameFinishEvent $event
     * @return \Indigo\GameBundle\Entity\Game
     */
    private function closeGame(GameFinishEvent $event)
    {
        printf("-------------------- GAME FINISH ----------------- \n");
        $gameEntity = $event->getGame();
        $gameEntity->setStatus(GameStatusRepository::STATUS_GAME_FINISHED);
        $gameEntity->setFinishedAt( new \DateTime());
        $gameEntity->getTableStatus()->setGame(null);
        return $gameEntity;
    }

}