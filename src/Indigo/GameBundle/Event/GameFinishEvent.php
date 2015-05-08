<?php

namespace Indigo\GameBundle\Event;


use Symfony\Component\EventDispatcher\Event;
use Indigo\GameBundle\Entity\Game;
use Indigo\GameBundle\Entity\TableStatus;
class GameFinishEvent extends Event
{
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