<?php

namespace Indigo\UIBundle\Models;

class ContestGamesViewModel implements \JsonSerializable
{
    /**
     * @var \ArrayIterator
     */
    private $games;

    public function __construct()
    {
        $this->games = new \ArrayIterator();
    }

    public function jsonSerialize() {

        return ['contest' => $this];
    }

    /**
     * @return \ArrayIterator
     */
    public function getGames()
    {
        return $this->games;
    }

    /**
     * @param \ArrayIterator $games
     */
    public function setGames($games)
    {
        $this->games = $games;
    }


    public function addGame(GameModel $game)
    {

        $this->games->append($game);
    }
}