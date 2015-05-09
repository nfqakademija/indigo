<?php

namespace Indigo\UIBundle\Models;

class GameModel
{
  /**
     * @var integer
     */
    private $team0Score;

    /**
     * @var integer
     */
    private $team1Score;

    /**
     * @var integer
     */
    private $gameDuration;

    /**
     * @var integer
     */
    private $team0Rating;

    /**
     * @var integer
     */
    private $team1Rating;

    /**
     * @var \ArrayIterator
     */
    private $team0playersPictures;

    /**
     * @var \ArrayIterator
     */
    private $team1playersPictures;

    public function __construct()
    {
        $this->team0playersPictures = new \ArrayIterator();
        $this->team1playersPictures = new \ArrayIterator();
    }

    /**
     * @param $picture
     * @return $this
     */
    public function addPlayerPicture($teamPosition, $picture)
    {
        if ($teamPosition) {

          $this->team1playersPictures->append($picture);
        } else {

          $this->team0playersPictures->append($picture);
        }

        return $this;
    }

    /**
     * @return int
     */
    public function getTeam0Score()
    {
        return $this->team0Score;
    }

  /**
   * @param $team0Score
   * @return $this
   */
    public function setTeam0Score($team0Score)
    {
        $this->team0Score = $team0Score;

        return $this;
    }

    /**
     * @return int
     */
    public function getTeam1Score()
    {
        return $this->team1Score;
    }

  /**
   * @param $team1Score
   * @return $this
   */
    public function setTeam1Score($team1Score)
    {
        $this->team1Score = $team1Score;

        return $this;
    }

    /**
     * @return int
     */
    public function getGameDuration()
    {
        return $this->gameDuration;
    }

  /**
   * @param $gameDuration
   * @return $this
   */
    public function setGameDuration($gameDuration)
    {
        $this->gameDuration = $gameDuration;

        return $this;

    }

    /**
     * @return int
     */
    public function getTeam0Rating()
    {
        return $this->team0Rating;
    }

  /**
   * @param $team0Rating
   * @return $this
   */
    public function setTeam0Rating($team0Rating)
    {
        $this->team0Rating = $team0Rating;

        return $this;
    }

    /**
     * @return int
     */
    public function getTeam1Rating()
    {
        return $this->team1Rating;
    }

  /**
   * @param $team1Rating
   * @return $this
   */
    public function setTeam1Rating($team1Rating)
    {
        $this->team1Rating = $team1Rating;

        return $this;
    }

    /**
     * @return \ArrayIterator
     */
    public function getTeam0playersPictures()
    {
        return $this->team0playersPictures;
    }

    /**
     * @param \ArrayIterator $team0playersPictures
     */
    public function setTeam0playersPictures($team0playersPictures)
    {
        $this->team0playersPictures = $team0playersPictures;
    }

    /**
     * @return \ArrayIterator
     */
    public function getTeam1playersPictures()
    {
        return $this->team1playersPictures;
    }

    /**
     * @param \ArrayIterator $team1playersPictures
     */
    public function setTeam1playersPictures($team1playersPictures)
    {
        $this->team1playersPictures = $team1playersPictures;
    }

}