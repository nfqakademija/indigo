<?php
/**
 * Created by PhpStorm.
 * User: TSU
 * Date: 2015.04.20
 * Time: 20:05
 */

namespace Indigo\UIBundle\Models;


class TeamModel implements \JsonSerializable {

    private $player1;
    private $player2;
    private $goals;
    private $imageUrl;

    public function __construct()
    {
        $this->goals = 0;
        $this->player1 = new PlayerModel();
        $this->player2 = new PlayerModel();
    }

    public function jsonSerialize() {
        return [
            "player1" => $this->player1,
            "player2" => $this->player2,
            "goals" => $this->goals,
            "imageUrl" => $this->imageUrl
        ];
    }

    /**
     * @return mixed
     */
    public function getImageUrl()
    {
        return $this->imageUrl;
    }

    /**
     * @param mixed $imageUrl
     */
    public function setImageUrl($imageUrl)
    {
        $this->imageUrl = $imageUrl;
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
     * @return mixed
     */
    public function getGoals()
    {
        return $this->goals;
    }

    /**
     * @param mixed $goals
     */
    public function setGoals($goals)
    {
        $this->goals = $goals;
    }
}