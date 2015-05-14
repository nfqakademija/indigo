<?php

namespace Indigo\UIBundle\Models;

class TeamViewModel
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

    /**
     * @var integer
     */
    private $teamRating;

    /**
     * @var \ArrayIterator
     */
    private $playersPictures;

    /**
     * @var integer
     */
    private $teamId;

    public function __construct()
    {
        $this->playersPictures = new \ArrayIterator();
        $this->setWins(0);
        $this->setLosses(0);
        $this->setScoredGoals(0);
        $this->setMissedGoals(0);
        $this->setTotalGames(0);
        $this->setTeamRating(0);

    }

    /**
     * @param $picture
     * @return $this
     */
    public function addPicture($picture)
    {
        $this->playersPictures->append($picture);

        return $this;
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

    private function addTotal()
    {
        $this->totalGames++;
    }

    /**
     * @param integer $goals
     */
    public function addScoredGoals($goals)
    {
        $this->scoredGoals += $goals;
    }

    /**
     * @param integer $goals
     */
    public function addMissedGoals($goals)
    {
        $this->missedGoals += $goals;
    }

    /**
     * @return \ArrayIterator
     */
    public function getPictures()
    {
        return $this->playersPictures;
    }

    /**
     * @param $pictures
     * @return $this
     */
    public function setPictures($pictures)
    {
        $this->playersPictures = $pictures;
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
     * @param $wins
     * @return $this
     */
    public function setWins($wins)
    {
        $this->wins = (int)$wins;

        return $this;
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

    /**
     * @return int
     */
    public function getTeamRating()
    {
        return $this->teamRating;
    }

    /**
     * @param $teamRating
     * @return $this
     */
    public function setTeamRating($teamRating)
    {
        $this->teamRating = $teamRating;

        return $this;
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