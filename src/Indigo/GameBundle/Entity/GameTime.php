<?php

namespace Indigo\GameBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * GameTime
 *
 * @ORM\Table(name="game_time")
 * @ORM\Entity(repositoryClass="Indigo\GameBundle\Entity\GameTimeRepository")
 */
class GameTime
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
     * @var \DateTime
     *
     * @ORM\Column(name="start_at", type="datetime")
     */
    private $startAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="finish_at", type="datetime")
     */
    private $finishAt;

    /**
     * @var integer
     *
     * @ORM\Column(name="reservation_id", type="integer")
     */
    private $reservationId;

    /**
     * @var integer
     *
     * @ORM\Column(name="reserved", type="integer")
     */
    private $reserved;


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
     * Set startAt
     *
     * @param \DateTime $startAt
     * @return GameTime
     */
    public function setStartAt($startAt)
    {
        $this->startAt = $startAt;

        return $this;
    }

    /**
     * Get startAt
     *
     * @return \DateTime 
     */
    public function getStartAt()
    {
        return $this->startAt;
    }

    /**
     * Set finishAt
     *
     * @param \DateTime $finishAt
     * @return GameTime
     */
    public function setFinishAt($finishAt)
    {
        $this->finishAt = $finishAt;

        return $this;
    }

    /**
     * Get finishAt
     *
     * @return \DateTime 
     */
    public function getFinishAt()
    {
        return $this->finishAt;
    }

    /**
     * Set reservationId
     *
     * @param integer $reservationId
     * @return GameTime
     */
    public function setReservationId($reservationId)
    {
        $this->reservationId = $reservationId;

        return $this;
    }

    /**
     * Get reservationId
     *
     * @return integer 
     */
    public function getReservationId()
    {
        return $this->reservationId;
    }

    /**
     * Set reserved
     *
     * @param integer $reserved
     * @return GameTime
     */
    public function setReserved($reserved)
    {
        $this->reserved = $reserved;

        return $this;
    }

    /**
     * Get reserved
     *
     * @return integer 
     */
    public function getReserved()
    {
        return $this->reserved;
    }
}
