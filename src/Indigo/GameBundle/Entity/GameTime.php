<?php

namespace Indigo\GameBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * GameTime
 *
 * @ORM\Table(name="game_times")
 * @ORM\Entity(repositoryClass="Indigo\GameBundle\Entity\GameTimeRepository")
 * @ORM\HasLifecycleCallbacks()
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
     * @var \Indigo\GameBundle\Entity\Game
     * @ORM\OneToMany(targetEntity="Indigo\GameBundle\Entity\Game",  mappedBy="gameTime", cascade={"persist"})
     * @ORM\JoinColumn(name="game_id", referencedColumnName="id")
     */
    private $games;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="start_at", type="datetime", nullable=false)
     */
    private $startAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="finish_at", type="datetime", nullable=false)
     */
    private $finishAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="insertion_time", type="datetime", nullable=false)
     */
    private $insertionTime;

    /**
     * User who reserved the time for game
     *
     * @var integer
     *
     * @ORM\Column(name="player_id", type="integer", nullable=false)
     */
    private $timeOwner;

    /**
     * User who reserved the time for game
     *
     * @var integer
     *
     * @ORM\Column(name="contest_id", type="integer", nullable=true)
     */
    private $contestId;

    /**
     *
     * @var integer
     *
     * @ORM\Column(name="ack", type="integer")
     */
    private $confirmed;


    public function __construct()
    {
        $this->games = new ArrayCollection();
    }

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
    public function setStartAtAndFinishAt($startAt = null)
    {
        $this->startAt = new \DateTime($startAt);
        $this->finishAt = (new \DateTime($startAt))->add(new \DateInterval('PT15M'));

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
    /*public function setFinishAt($startAt)
    {
        $explode = explode(" - ", $startAt);
        $this->finishAt = new \DateTime($explode[1]);

        return $this;
    }*/

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
     * @param $timeOwner
     * @return $this
     */
    public function setTimeOwner($timeOwner)
    {
        $this->timeOwner = $timeOwner;

        return $this;
    }

    /**
     * Get TimeOwner
     *
     * @return integer
     */
    public function getTimeOwner()
    {
        return $this->timeOwner;
    }

    /**
     * @return int
     */
    public function getConfirmed()
    {
        return $this->confirmed;
    }

    /**
     * @param int $confirmed
     */
    public function setConfirmed($confirmed)
    {
        $this->confirmed = $confirmed;
    }

    /**
     * @return Game
     */
    public function getGames()
    {
        return $this->games;
    }

    /**
     * @param ArrayCollection $games
     */
    public function setGames(ArrayCollection $games)
    {
        $this->games = $games;
    }

    /**
     * @param Game $games
     * @return $this
     */
    public function addGame(Game $games)
    {
        $this->games->add($games);

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getInsertionTime()
    {
        return $this->insertionTime;
    }

    /**
     * @param \DateTime $insertionTime
     */
    public function setInsertionTime($insertionTime)
    {
        $this->insertionTime = new \Datetime($insertionTime);
    }

    /**
     * @return int
     */
    public function getContestId()
    {
        return $this->contestId;
    }

    /**
     * @param int $contestId
     */
    public function setContestId($contestId)
    {
        $this->contestId = $contestId;
    }
}
