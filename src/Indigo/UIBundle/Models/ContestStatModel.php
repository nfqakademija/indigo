<?php

namespace Indigo\UIBundle\Models;


class ContestStatModel implements \JsonSerializable
{

    /**
     * @var integer
     */
    private $totalGames;

    /**
     * @var integer
     */
    private $totalGoals;

    /**
     * @var integer
     */
    private $totalTeams;

    /**
     * @var integer
     */
    private $fastestGameDuration;

    /**
     * @var integer
     */
    private $slowestGameDuration;

    /**
     * @var \ArrayIterator
     */
    private $topTeams;





    public function __construct()
    {
        $this->topTeams = new \ArrayIterator();
    }

    /**
     * @param TeamViewModel $topTeam
     * @param $position
     * @return $this
     */
    public function addTopTeam(TeamViewModel $topTeam, $position)
    {
        $this->topTeams->offsetSet((int)$position, $topTeam);

        return $this;
    }

    /**
     * @param $goals
     * @return $this
     */
    public function incTotalGoals($goals)
    {
        $this->setTotalGoals($this->getTotalGoals() + (int)$goals);

        return $this;
    }

    /**
     * @return $this
     */
    public function incTotalGames()
    {
        $this->setTotalGames($this->getTotalGames()+1);
        return $this;
    }

    /**
     * @return $this
     */
    public function incUniqTeams()
    {
        $this->setTotalTeams($this->getTotalTeams()+1);
        return $this;
    }


    /**
     * @return $this
     */
    public function jsonSerialize()
    {
        return $this;
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
     * @return \ArrayIterator
     */
    public function getTopTeams()
    {
        return $this->topTeams;
    }

    /**
     * @param \ArrayIterator $topTeams
     */
    public function setTopTeams(\ArrayIterator $topTeams)
    {
        $this->topTeams = $topTeams;
    }


    /**
     * @param $position
     * @return mixed
     */
    public function getTopTeam($position)
    {
        if ($this->topTeams->offsetExists((int)$position)) {

            return $this->topTeams->offsetGet((int)$position);
        }

        return null;
    }

    /**
     * @return int
     */
    public function getTotalGoals()
    {
        return $this->totalGoals;
    }

    /**
     * @param int $totalGoals
     */
    public function setTotalGoals($totalGoals)
    {
        $this->totalGoals = $totalGoals;
    }

    /**
     * @return mixed
     */
    public function getTotalTeams()
    {
        return $this->totalTeams;
    }

    /**
     * @param mixed $totalTeams
     */
    public function setTotalTeams($totalTeams)
    {
        $this->totalTeams = $totalTeams;
    }

    /**
     * @return int
     */
    public function getFastestGameDuration()
    {
        return $this->fastestGameDuration;
    }

    /**
     * @param $fastestGameDuration
     * @return $this
     */
    public function setFastestGameDuration($fastestGameDuration)
    {
        $this->fastestGameDuration = (int)$fastestGameDuration;

        return $this;
    }

    /**
     * @return int
     */
    public function getSlowestGameDuration()
    {
        return $this->slowestGameDuration;
    }

    /**
     * @param $slowestGameDuration
     * @return $this
     */
    public function setSlowestGameDuration($slowestGameDuration)
    {
        $this->slowestGameDuration = (int)$slowestGameDuration;

        return $this;
    }


}