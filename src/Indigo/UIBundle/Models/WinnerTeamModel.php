<?php
/**
 * Created by PhpStorm.
 * User: TSU
 * Date: 2015.05.05
 * Time: 23:24
 */

namespace Indigo\UIBundle\Models;


class WinnerTeamModel implements \JsonSerializable {
    /***
     * @var PlayerModel
     */
    private $player1;
    /**
     * @var PlayerModel
     */
    private $player2;

    /**
     * @var string
     */
    private $score;

    public function __construct()
    {
        $this->score ="0 - 0";
        $this->player1 = new PlayerModel();
        $this->player1->setImageUrl("/bundles/indigoui/images/anonymous.png");
        $this->player2 = new PlayerModel("/bundles/indigoui/images/anonymous.png");
        $this->player2->setImageUrl("/bundles/indigoui/images/anonymous.png");

    }

    public function jsonSerialize() {
        return [
            "player1" => $this->player1,
            "player2" => $this->player2,
            "score" => $this->score
            ];
    }

    /**
     * @return mixed
     */
    public function getPlayer1()
    {
        return $this->player1;
    }

    /**
     * @param mixed $player1
     */
    public function setPlayer1($player1)
    {
        $this->player1 = $player1;
    }

    /**
     * @return mixed
     */
    public function getPlayer2()
    {
        return $this->player2;
    }

    /**
     * @param mixed $player2
     */
    public function setPlayer2($player2)
    {
        $this->player2 = $player2;
    }

    /**
     * @return string
     */
    public function getScore()
    {
        return $this->score;
    }

    /**
     * @param string $score
     */
    public function setScore($score)
    {
        $this->score = $score;
    }


}