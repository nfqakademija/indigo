<?php

namespace Indigo\ApiBundle\Model;

/*
        {"id":"96512","timeSec":"1425543551","usec":"169652","type":"AutoGoal",
            "data":"{\u0022team\u0022:1}"},
*/
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