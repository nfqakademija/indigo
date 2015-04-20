<?php
/**
 * Created by PhpStorm.
 * User: TSU
 * Date: 2015.04.20
 * Time: 20:14
 */



namespace Indigo\UIBundle\Models;
use JsonSerializable;

class LiveViewModel {

    private $contest;
    private $isTableBusy;
    private $teamA;
    private $teamB;

    public function __construct()
    {
        $this->isTableBusy = false;
        $this->contest = new ContestModel();
        $this->teamA = new TeamModel();
        $this->teamB = new TeamModel();
    }


    public function jsonSerialize() {
        return [
            "isBusy" => $this->isTableBusy,
            "contest" => $this->contest->jsonSerialize(),
            "teamA" => $this->teamA->jsonSerialize(),
            "teamB" => $this->teamB->jsonSerialize()
        ];
    }

    /**
     * @return TeamModel
     */
    public function getTeamA()
    {
        return $this->teamA;
    }

    /**
     * @param TeamModel $teamA
     */
    public function setTeamA($teamA)
    {
        $this->teamA = $teamA;
    }

    /**
     * @return TeamModel
     */
    public function getTeamB()
    {
        return $this->teamB;
    }

    /**
     * @param TeamModel $teamB
     */
    public function setTeamB($teamB)
    {
        $this->teamB = $teamB;
    }
    private $reservations = array();


    /**
     * @return mixed
     */
    public function getContest()
    {
        return $this->contest;
    }

    /**
     * @param mixed $contest
     */
    public function setContest($contest)
    {
        $this->contest = $contest;
    }

    /**
     * @return mixed
     */
    public function getIsTableBusy()
    {
        return $this->isTableBusy;
    }

    /**
     * @param mixed $isTableBusy
     */
    public function setIsTableBusy($isTableBusy)
    {
        $this->isTableBusy = $isTableBusy;
    }

    /**
     * @return array
     */
    public function getReservations()
    {
        return $this->reservations;
    }

    /**
     * @param array $reservations
     */
    public function setReservations($reservations)
    {
        $this->reservations = $reservations;
    }
}