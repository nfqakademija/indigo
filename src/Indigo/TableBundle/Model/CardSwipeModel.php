<?php


namespace Indigo\TableBundle\Model;


class CardSwipeModel extends TableActionModel implements TableActionInterface
{
    /**
     * @var int
     */
    private $team;

    /**
     * @var int
     */
    private $player;

    /**
     * @var int
     */
    private $card_id;

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
        $this->team = (int)$team;
    }

    /**
     * @return mixed
     */
    public function getPlayer()
    {
        return $this->player;
    }

    /**
     * @param int $player
     */
    public function setPlayer($player)
    {
        $this->player = (int)$player;
    }

    /**
     * @return int
     */
    public function getCardId()
    {
        return $this->card_id;
    }

    /**
     * @param int $card_id
     */
    public function setCardId($card_id)
    {
        $this->card_id = (int)$card_id;
    }

    /**
     * @param \stdClass $data
     */
    public function setData(\stdClass $data)
    {
        $this->setTeam($data->team);
        $this->setPlayer($data->player);
        $this->setCardId($data->card_id);
    }
}