<?php

namespace Indigo\GameBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TableStatus
 *
 * @ORM\Table(name="table_status")
 * @ORM\Entity(repositoryClass="Indigo\GameBundle\Entity\TableStatusRepository")
 */
class TableStatus
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="busy", type="integer")
     */
    private $busy;

    /**
     * @var integer
     *
     * @ORM\Column(name="game_id", type="integer")
     */
    private $gameId;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set busy
     *
     * @param integer $busy
     * @return TableStatus
     */
    public function setBusy($busy)
    {
        $this->busy = $busy;

        return $this;
    }

    /**
     * Get busy
     *
     * @return integer 
     */
    public function getBusy()
    {
        return $this->busy;
    }

    /**
     * Set gameId
     *
     * @param integer $gameId
     * @return TableStatus
     */
    public function setGameId($gameId)
    {
        $this->gameId = $gameId;

        return $this;
    }

    /**
     * Get gameId
     *
     * @return integer 
     */
    public function getGameId()
    {
        return $this->gameId;
    }
}
