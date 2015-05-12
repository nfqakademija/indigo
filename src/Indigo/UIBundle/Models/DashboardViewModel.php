<?php

namespace Indigo\UIBundle\Models;
use JsonSerializable;

class DashboardViewModel implements \JsonSerializable
{
    /**
     * @var ArrayIterator
     */
    private $playerTeamsStats;

    /**
     * @var ContestStatModel
     */
    private $contestStat;

    /**
     * @var ContestModel
     */
    private $currentContest;

    /**
     * @var ContestModel
     */
    private $nextContest;

    /**
     * @var ReservationModel
     */
    private $nextReservation;

    /**
     * @var int
     */
    private $teamAScore;

    /**
     * @var int
     */
    private $teamBScore;

    /**
     * @var boolean
     */
    private $isTableBusy;

    public function __construct()
    {

        $this->currentContest = new ContestModel();
        $this->nextContest = new ContestModel();
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {

        return [
            "currentContest" => $this->currentContest,
            "nextContest" => $this->nextContest,
            "nextReservation" => $this->nextReservation,
            "isTableBusy" => $this->isTableBusy,
            "teamAScore" => $this->teamAScore,
            "teamBScore" => $this->teamBScore,
            'playerTeamsStat' => $this->getPlayerTeamsStats(),
            'contestStat' => $this->getContestStat()
        ];
    }

    /**
     * @return mixed
     */
    public function getPlayerTeamsStats()
    {
        return $this->playerTeamsStats;
    }

    /**
     * @param \ArrayIterator $stats
     */
    public function setPlayerTeamsStats(\ArrayIterator $stats)
    {
        $this->playerTeamsStats = $stats;
    }

    /**
     * @return boolean
     */
    public function isIsTableBusy()
    {
        return $this->isTableBusy;
    }

    /**
     * @param boolean $isTableBusy
     */
    public function setIsTableBusy($isTableBusy)
    {
        $this->isTableBusy = $isTableBusy;
    }

    /**
     * @return int
     */
    public function getTeamAScore()
    {
        return $this->teamAScore;
    }

    /**
     * @param int $teamAScore
     */
    public function setTeamAScore($teamAScore)
    {
        $this->teamAScore = $teamAScore;
    }

    /**
     * @return int
     */
    public function getTeamBScore()
    {
        return $this->teamBScore;
    }

    /**
     * @param int $teamBScore
     */
    public function setTeamBScore($teamBScore)
    {
        $this->teamBScore = $teamBScore;
    }

    /**
     * @return ContestModel
     */
    public function getCurrentContest()
    {
        return $this->currentContest;
    }

    /**
     * @param ContestModel $currentContest
     */
    public function setCurrentContest($currentContest)
    {
        $this->currentContest = $currentContest;
    }

    /**
     * @return ContestModel
     */
    public function getNextContest()
    {
        return $this->nextContest;
    }

    /**
     * @param ContestModel $nextContest
     */
    public function setNextContest($nextContest)
    {
        $this->nextContest = $nextContest;
    }

    /**
     * @return ReservationModel
     */
    public function getNextReservation()
    {
        return $this->nextReservation;
    }

    /**
     * @param ReservationModel $nextReservation
     */
    public function setNextReservation($nextReservation)
    {
        $this->nextReservation = $nextReservation;
    }

    /**
     * @return ContestStatModel
     */
    public function getContestStat()
    {
        return $this->contestStat;
    }

    /**
     * @param ContestStatModel $contestStat
     */
    public function setContestStat($contestStat)
    {
        $this->contestStat = $contestStat;
    }

}