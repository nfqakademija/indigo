<?php

namespace Indigo\TableBundle\Model;

class AutoGoalModel extends TableActionModel implements TableActionInterface
{
    /**
     * @var int
     */
    private $team;

    /**
     * @return int
     */
    public function getTeam()
    {
        return $this->team;
    }

    /**
     * @param int $team
     */
    public function setTeam($team)
    {
        $this->team = (int) $team;
    }

    /**
     * @param \stdClass $data
     */
    public function setData(\stdClass $data)
    {
        $this->setTeam($data->team);

    }
}