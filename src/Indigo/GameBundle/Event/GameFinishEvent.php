<?php
/**
 * Created by PhpStorm.
 * User: simpleuser
 * Date: 4/11/2015
 * Time: 4:13 PM
 */

namespace Indigo\GameBundle\Event;


use Symfony\Component\EventDispatcher\Event;
use Indigo\GameBundle\Entity\Game;
class GameFinishEvent extends Event{

    /**
     * @var Game
     */
    private $game;

    /**
     * @var TableStatus
     */
    private $tableStatus;

    /**
     * @param Game $game
     */
    public function setGame(Game $game)
    {
        $this->game = $game;
    }

    /**
     * @return Game
     */
    public function getGame()
    {
        return $this->game;
    }

    /**
     * @return TableStatus
     */
    public function getTableStatus()
    {
        return $this->tableStatus;
    }

    /**
     * @param TableStatus $tableStatus
     * @return $this
     */
    public function setTableStatus(TableStatus $tableStatus)
    {
        $this->tableStatus = $tableStatus;
        return $this;
    }



}