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
     * @ORM\Column(name="id", type="integer", options={"unsigned":true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var \Indigo\GameBundle\Entity\Game
     * @ORM\OneToMany(targetEntity="Indigo\GameBundle\Entity\Game", mappedBy="gameTime")
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
     *
     * @var integer
     *
     * @ORM\Column(name="ack", type="integer")
     */
    private $confirmed;

    /**
     * @ORM\ManyToOne(targetEntity="Indigo\ContestBundle\Entity\Contest", inversedBy="gameTimes")
     * @ORM\JoinColumn(name="contest_id", referencedColumnName="id")
     */
    private $contest;

    /**
     * @ORM\Column(name="contest_id", type="integer", nullable=false, options={"unsigned":true})
     */
    private $contestId;



    public function __construct()
    {
        $this->games = new ArrayCollection();
    }

    /**
     * @ORM\PrePersist()
     */
    public function prePersist()
    {
        if ($this->getStartAt() === null) {
            $this->setStartAt(new \DateTime());
            $this->setFinishAt(new \DateTime('+15 minutes'));
        }

        if ($this->getTimeOwner() === null) {
            $this->setTimeOwner(0);
        }

        if ($this->getConfirmed() === null) {
            $this->setConfirmed(0);
        }
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
     * @return Game
     */
    public function getGame()
    {
        return $this->game;
    }

    /**
     * @param $game
     * @return $this
     */
    public function setGame($game)
    {
        $this->game = $game;
        return $this;
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
     * @param Game $game
     * @return $this
     */
    public function addGame(Game $game)
    {
        $this->games->add($game);

        return $this;
    }

    /**
     * @return mixed
     */
    public function getContest()
    {
        return $this->contest;
    }

    /**
     * @param Contest $contest
     * @return $this
     */
    public function setContest(Contest $contest)
    {
        $this->contest = $contest;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getContestId()
    {
        return $this->contestId;
    }

    /**
     * @param mixed $contestId
     */
    public function setContestId($contestId)
    {
        $this->contestId = (int)$contestId;
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


}
