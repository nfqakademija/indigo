<?php

namespace Indigo\UIBundle\Models;

use Indigo\GameBundle\Entity\TeamRepository;

class PlayerTeamStatModel extends TeamViewModel
{
    const DEFAULT_PWIN = 0.5;

    /**
     * @var integer
     */
    private $teamId;

    /**
     * @var integer
     */
    private $fastestWinGameTs;

    /**
     * @var integer
     */
    private $slowestWinGameTs;

    /**
     * @return string
     */
    public function getProbabilityOfWin()
    {
        return sprintf("%.2f", (($this->getWins() + self::DEFAULT_PWIN) / ($this->getTotalGames() + 1)));
    }


    public function __construct()
    {

        $this->setTeamRating(TeamRepository::DEFAULT_RATING);
        $this->setSlowestWinGameTs(0);
        $this->setFastestWinGameTs(0);
        parent::__construct();
    }

    public function jsonSerialize()
    {
        return $this;
    }

    /**
     * @return int
     */
    public function getFastestWinGameTs()
    {
        return $this->fastestWinGameTs;
    }

    /**
     * @param int $fastestWinGameTs
     */
    public function setFastestWinGameTs($fastestWinGameTs)
    {
        $this->fastestWinGameTs = $fastestWinGameTs;
    }

    /**
     * @return int
     */
    public function getSlowestWinGameTs()
    {
        return $this->slowestWinGameTs;
    }

    /**
     * @param int $slowestWinGameTs
     */
    public function setSlowestWinGameTs($slowestWinGameTs)
    {
        $this->slowestWinGameTs = $slowestWinGameTs;
    }

    /**
     * @return int
     */
    public function getTeamId()
    {
        return $this->teamId;
    }

    /**
     * @param int $teamId
     */
    public function setTeamId($teamId)
    {
        $this->teamId = $teamId;
    }
}