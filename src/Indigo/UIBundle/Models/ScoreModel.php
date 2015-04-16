<?php
/**
 * Created by PhpStorm.
 * User: TSU
 * Date: 2015.04.17
 * Time: 00:51
 */

namespace Indigo\UIBundle\Models;


class ScoreModel {
    private $teamA;
    private $teamB;

    /**
     * @return mixed
     */
    public function getTeamA()
    {
        return $this->teamA;
    }

    /**
     * @param mixed $teamA
     */
    public function setTeamA($teamA)
    {
        $this->teamA = $teamA;
    }

    /**
     * @return mixed
     */
    public function getTeamB()
    {
        return $this->teamB;
    }

    /**
     * @param mixed $teamB
     */
    public function setTeamB($teamB)
    {
        $this->teamB = $teamB;
    }

}