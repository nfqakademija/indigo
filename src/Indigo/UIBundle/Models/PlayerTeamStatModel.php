<?php

namespace Indigo\UIBundle\Models;


use Indigo\GameBundle\Entity\TeamRepository;

class PlayerTeamStatModel
{
    const DEFAULT_PWIN = 0.5;
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

    /**
     * @var integer
     */
    private $fastestWinGameTs;

    /**
     * @var integer
     */
    private $teamRating;

    /**
     * @var integer
     */
    private $slowestWinGameTs;

    /**
     * Probability of win
     * @var integer
     */
    private $pWin;


    public function getProbabilityOfWin ()
    {
        if ($this->getTotalGames()) {

            $this->setPWin( $this->getWins() / $this->getTotalGames());
        }
    }


    public function addWins()
    {
        $this->wins++;
        $this->addTotal();
    }

    public function addLosses()
    {
        $this->losses++;
        $this->addTotal();
    }

    public function addDraws()
    {
        $this->draws++;
        $this->addTotal();
    }

    private function addTotal()
    {
        $this->totalGames++;
    }

    public function addScoredGoals($goals)
    {
        $this->scoredGoals += $goals;
    }

    public function addMissedGoals($goals)
    {
        $this->missedGoals += $goals;
    }

    public function __construct()
    {
        $this->setWins(0);
        $this->setLosses(0);
        $this->setScoredGoals(0);
        $this->setMissedGoals(0);
        $this->setTeamRating(TeamRepository::DEFAULT_RATING);
        $this->setSlowestWinGameTs(0);
        $this->setFastestWinGameTs(0);
        $this->setPWin(self::DEFAULT_PWIN);
    }

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
     * @param mixed $loses
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
    public function getTeamRating()
    {
        return $this->teamRating;
    }

    /**
     * @param int $teamRating
     */
    public function setTeamRating($teamRating)
    {
        $this->teamRating = $teamRating;
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
     * @return mixed
     */
    public function getPWin()
    {
        return $this->pWin;
    }

    /**
     * @param mixed $pWin
     */
    public function setPWin($pWin)
    {
        $this->pWin = $pWin;
    }


}