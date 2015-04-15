<?php
/**
 * Created by PhpStorm.
 * User: simpleuser
 * Date: 4/11/2015
 * Time: 4:39 PM
 */

namespace Indigo\GameBundle\EventListener;


use Doctrine\ORM\EntityManagerInterface;
use Indigo\GameBundle\Event\GameFinishEvent;
use Indigo\GameBundle\Repository\GameStatusRepository;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;


class GameFinishListener {

//    /**
//     * @var EventDispatcher
//     */
//    private $ed;
//
//    /**
//     * @param EventDispatcher $ed
//     */
//    public function __construct(EntityManagerInterface $ed)
//    {
//        $this->ed = $ed;
//    }

    /**
     * @param GameFinishEvent $event
     */
    public function onGameFinish(GameFinishEvent $event)
    {
        $this->closeGame($event);

    }

    /**
     * @param GameFinishEvent $event
     */
    public function onGameFinishMaxScoreReached(GameFinishEvent $event)
    {
        $this->closeGame($event);
        //TODO: sukurti analogiska game'a

/*
 *  naujo zaidimo sukurimas,
 *  senu zaideju sudejimas

        $newGameEntity = new Game();

        $newGameTimeEntity = new GameTime();
        $newGameEntity->
        $newGameEntity->setGameTime($gameTimeEntity);

        $newGameEntity = $this->createGame($tableStatusEntity, GameStatusRepository::STATUS_GAME_STARTED);

        //sukurti is kart nauja game'a
        $tableStatusEntity->addGame()*/

    }

    /**
     * @param GameFinishEvent $event
     */
    private function closeGame(GameFinishEvent $event)
    {
        $gameEntity = $event->getGame();
        $gameEntity->setStatus(GameStatusRepository::STATUS_GAME_FINISHED);
        $gameEntity->setFinishedAt( new \DateTime());

        // TODO: panaikinti relationa su tablestatus'u
        //$gameEntity->setTableStatus()
        $this->onRatingCalculate($event);
    }

    /**
     * @param GameFinishEvent $event
     */
    public function onRatingCalculate(GameFinishEvent $event)
    {
        // smth
    }
}