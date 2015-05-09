<?php

namespace Indigo\UIBundle\Models;

class GameHistoryViewModel implements \JsonSerializable
{
    /**
     * @var \ArrayIterator
     */
    private $games;

    public function __construct()
    {
        $this->games = new \ArrayIterator();
    }


    public function  jsonSerialize ()
    {
         return [
             'games' => $this->getGames()
        ];
    }
  /**
   * @param GameModel $game
   * @return $this
   */
    public function addGame(GameModel $game)
    {
        $this->games->append($game);

        return $this;
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
}