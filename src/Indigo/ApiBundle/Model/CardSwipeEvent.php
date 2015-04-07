<?php


namespace Indigo\ApiBundle\Model;


class CardSwipeEvent extends TableEvent implements TableEventInterface
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
     * @param array $data
     */
    public function setData(array $data)
    {
        $this->setTeam($data['team']);
        $this->setPlayer($data['player']);
        $this->setCardId($data['card_id']);
    }

    /*
            {"id":"96515","timeSec":"1425543560","usec":"485733","type":"CardSwipe",
                "data":"{\u0022team\u0022:0,\u0022player\u0022:1,\u0022card_id\u0022:8469951}"},
    */

}