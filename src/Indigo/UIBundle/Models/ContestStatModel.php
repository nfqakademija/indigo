<?php

namespace Indigo\UIBundle\Models;


class ContestStatModel implements \JsonSerializable
{
    /**
     * @var integer
     */
    private $wins;

    /**
     * @var integer
     */
    private $losses;

    /**
     * @var integer
     */
    private $totalGames;

    /**
     * @var integer
     */
    private $scoredGoals;

    /**
     * @var integer
     */
    private $missedGoals;



    public function jsonSerialize()
    {
        return $this;
    }

    /**
     * @return int
     */
    public function getWins()
    {
        return $this->wins;
    }

    /**
     * @param mixed $wins
     */
    public function setWins($wins)
    {
        $this->wins = (int)$wins;
    }

    /**
     * @return int
     */
    public function getLosses()
    {
        return $this->losses;
    }

    /**
     * @param $losses
     */
    public function setLosses($losses)
    {
        $this->losses = (int)$losses;
    }

    /**
     * @return int
     */
    public function getTotalGames()
    {
        return $this->totalGames;
    }

    /**
     * @param $totalGames
     */
    public function setTotalGames($totalGames)
    {
        $this->totalGames = (int)$totalGames;
    }

    /**
     * @return int
     */
    public function getScoredGoals()
    {
        return $this->scoredGoals;
    }

    /**
     * @param $scoredGoals
     */
    public function setScoredGoals($scoredGoals)
    {
        $this->scoredGoals = (int)$scoredGoals;
    }

    /**
     * @return int
     */
    public function getMissedGoals()
    {
        return $this->missedGoals;
    }

    /**
     * @param $missedGoals
     */
    public function setMissedGoals($missedGoals)
    {
        $this->missedGoals = (int)$missedGoals;
    }
}