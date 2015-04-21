<?php
/**
 * Created by PhpStorm.
 * User: TSU
 * Date: 2015.04.20
 * Time: 20:36
 */

namespace Indigo\UIBundle\Models;


class ReservationModel implements \JsonSerializable{

    private $contestName;
    private $dateStart;
    private $dateEnd;
    private $patron;

    public function __construct()
    {

    }

    public function jsonSerialize() {
        return [
            "contestName" => $this->contestName,
            "dateStart" => $this->dateStart,
            "dateEnd" => $this->dateEnd,
            "patron" => $this->patron
        ];
    }


    /**
     * @return mixed
     */
    public function getContestName()
    {
        return $this->contestName;
    }

    /**
     * @param mixed $contestName
     */
    public function setContestName($contestName)
    {
        $this->contestName = $contestName;
    }

    /**
     * @return mixed
     */
    public function getDateStart()
    {
        return $this->dateStart;
    }

    /**
     * @param mixed $dateStart
     */
    public function setDateStart($dateStart)
    {
        $this->dateStart = $dateStart;
    }

    /**
     * @return mixed
     */
    public function getDateEnd()
    {
        return $this->dateEnd;
    }

    /**
     * @param mixed $dateEnd
     */
    public function setDateEnd($dateEnd)
    {
        $this->dateEnd = $dateEnd;
    }

    /**
     * @return mixed
     */
    public function getPatron()
    {
        return $this->patron;
    }

    /**
     * @param mixed $patron
     */
    public function setPatron($patron)
    {
        $this->patron = $patron;
    }

}