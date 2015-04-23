<?php
/**
 * Created by PhpStorm.
 * User: TSU
 * Date: 2015.04.20
 * Time: 20:14
 */



namespace Indigo\UIBundle\Models;
use JsonSerializable;

class LiveViewModel implements \JsonSerializable{

    /**
     * @var ContestModel
     */
    private $contest;
    /**
     * @var boolean
     */
    private $isTableBusy;
    /**
     * @var TeamModel
     */
    private $teamA;
    /**
     * @var TeamModel
     */
    private $teamB;

    /**
     * @var string
     */
    private $statusMessage;

    public function __construct()
    {
        $this->isTableBusy = false;
        $this->contest = new ContestModel();
        $this->teamA = new TeamModel();
        $this->teamB = new TeamModel();
    }

    /**
     * @return array
     */
    public function jsonSerialize() {
        return [
            "isBusy" => $this->isTableBusy,
            "statusMessage" => $this->statusMessage,
            "contest" => $this->contest,
            "teamA" => $this->teamA,
            "teamB" => $this->teamB
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

    /**
     * @return string
     */
    public function getStatusMessage()
    {
        return $this->statusMessage;
    }

    /**
     * @param string $statusMessage
     */
    public function setStatusMessage($statusMessage)
    {
        $this->statusMessage = $statusMessage;
    }
}