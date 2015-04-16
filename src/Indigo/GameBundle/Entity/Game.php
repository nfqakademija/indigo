<?php

namespace Indigo\GameBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Indigo\GameBundle\Repository\GameStatusRepository;
use Indigo\GameBundle\Repository\GameTypeRepository;

/**
 * Game
 *
 * @ORM\Table(name="games")
 * @ORM\Entity(repositoryClass="Indigo\GameBundle\Entity\GameRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Game
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
     * @ORM\Column(name="table_status_id", type="integer")
     */
    private $tableStatusId;

    /**
     * @var \Indigo\GameBundle\Entity\TableStatus
     * @ORM\ManyToOne(targetEntity="Indigo\GameBundle\Entity\TableStatus", inversedBy="games", cascade={"all"})
     * @ORM\JoinColumn(name="table_status_id", referencedColumnName="id")
     */
    private $tableStatus;

    /**
     * @var \Indigo\GameBundle\Entity\GameTime
     *
     * @ORM\OneToOne(targetEntity="Indigo\GameBundle\Entity\GameTime", inversedBy="game")
     * @ORM\JoinColumn(name="game_time_id", referencedColumnName="id")
     */
    private $gameTime;

    /**
     * @var \Indigo\UserBundle\Entity\User
     * @ORM\ManyToOne(targetEntity="Indigo\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="team0_player0_id", referencedColumnName="id")
     */
    private $team0Player0Id;

    /**
     * @var \Indigo\UserBundle\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="Indigo\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="team0_player1_id", referencedColumnName="id")
     */
    private $team0Player1Id;

    /**
     * @var \Indigo\UserBundle\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="Indigo\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="team1_player0_id", referencedColumnName="id")
     */
    private $team1Player0Id;

    /**
     * @var \Indigo\UserBundle\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="Indigo\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="team1_player1_id", referencedColumnName="id")
     */
    private $team1Player1Id;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", length=255)
     * columnDefinition="ENUM('playing', 'waiting', 'finished', 'zombie')"
     */
    private $status;

    /**
     * @var integer
     *
     * @ORM\Column(name="team0_score", type="integer", options={"default"=0})
     */
    private $team0Score;

    /**
     * @var integer
     *
     * @ORM\Column(name="team1_score", type="integer", options={"default"=0})
     */
    private $team1Score;

    /**
     * @var integer
     *
     * @ORM\Column(name="team0_id", type="integer", options={"default"=0})
     */
    private $team0Id;

    /**
     * @var integer
     *
     * @ORM\Column(name="team1_id", type="integer", options={"default"=0})
     */
    private $team1Id;

    /**
     * @var string
     *
     * @ORM\Column(name="match_type", type="string")
     * columnDefinition="ENUM('single', 'team', 'open')"
     */
    private $matchType;

    /**
     * @var integer
     *
     * @ORM\Column(name="contest_id", type="integer", options={"default"=0})
     */
    private $contestId;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="started_at", type="datetime")
     */
    private $startedAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="finished_at", type="datetime", nullable=true)
     */
    private $finishedAt;

    public function __construct()
    {
        $this->setTeam0Id(0);
        $this->setTeam0Score(0);
        $this->setTeam1Id(0);
        $this->setTeam1Score(0);

        $this->setContestId(0);
        $this->setChallengeId(0);
    }

    /**
     * @ORM\PrePersist()
     */
    public function prePersist()
    {
        if ($this->getStartedAt() === null) {
            $this->setStartedAt(new \DateTime());
        }

        if ($this->getMatchType() === null) {
            $this->setMatchType(GameTypeRepository::TYPE_GAME_OPEN);
        }

        if ($this->getStatus() === null) {
            $this->setStatus(GameStatusRepository::STATUS_GAME_WAITING);
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
     * @param TableStatus $tableStatus
     * @return $this
     */
    public function setTableStatus(TableStatus $tableStatus)
    {
        $this->tableStatus = $tableStatus;

        return $this;
    }

    /**
     * @return TableStatus
     */
    public function getTableStatus()
    {
        return $this->tableStatus;
    }

    /**
     * @return int
     */
    public function getTableStatusId()
    {
        return $this->tableStatusId;
    }

    /**
     * @param $tableStatusId
     * @return $this
     */
    public function setTableStatusId($tableStatusId)
    {
        $this->tableStatusId = $tableStatusId;
        return $this;
    }

    /**
     * Set gametimeId
     *
<<<<<<< HEAD
     * @param \Indigo\GameBundle\Entity\GameTime $gametimeId
     * @return Game
     */
    public function setGametimeId(\Indigo\GameBundle\Entity\GameTime $gametimeId)
    {
        $this->gametimeId = $gametimeId;
=======
     * @param \Indigo\GameBundle\Entity\GameTime $gameTime
     * @return Game
     */
    public function setGameTime(\Indigo\GameBundle\Entity\GameTime $gameTime)
    {
        $this->gameTime = $gameTime;
>>>>>>> api_test

        return $this;
    }

    /**
     * Get gametimeId
     *
     * @return \Indigo\GameBundle\Entity\GameTime
     */
    public function getGameTime()
    {
        return $this->gameTime;
    }

    /**
     * Set team0Player0Id
     *
     * @param \Indigo\UserBundle\Entity\User $team0Player0Id
     * @return Game
     */
    public function setTeam0Player0Id(\Indigo\UserBundle\Entity\User $team0Player0Id)
    {
        $this->team0Player0Id = $team0Player0Id;

        return $this;
    }

    /**
     * Get team0Player0Id
     *
     * @return \Indigo\UserBundle\Entity\User
     */
    public function getTeam0Player0Id()
    {
        return $this->team0Player0Id;
    }

    /**
     * Set team0Player1Id
     *
     * @param \Indigo\UserBundle\Entity\User $team0Player1Id
     * @return Game
     */
    public function setTeam0Player1Id(\Indigo\UserBundle\Entity\User  $team0Player1Id)
    {
        $this->team0Player1Id = $team0Player1Id;

        return $this;
    }

    /**
     * Get team0Player1Id
     *
     * @return \Indigo\UserBundle\Entity\User
     */
    public function getTeam0Player1Id()
    {
        return $this->team0Player1Id;
    }

    /**
     * Set team1Player0Id
     *
     * @param \Indigo\UserBundle\Entity\User $team1Player0Id
     * @return Game
     */
    public function setTeam1Player0Id(\Indigo\UserBundle\Entity\User  $team1Player0Id)
    {
        $this->team1Player0Id = $team1Player0Id;

        return $this;
    }

    /**
     * Get team1Player0Id
     *
     * @return \Indigo\UserBundle\Entity\User
     */
    public function getTeam1Player0Id()
    {
        return $this->team1Player0Id;
    }

    /**
     * Set team1Player1Id
     *
     * @param \Indigo\UserBundle\Entity\User $team1Player1Id
     * @return Game
     */
    public function setTeam1Player1Id(\Indigo\UserBundle\Entity\User  $team1Player1Id)
    {
        $this->team1Player1Id = $team1Player1Id;

        return $this;
    }

    /**
     * Get team1Player1Id
     *
     * @return \Indigo\UserBundle\Entity\User
     */
    public function getTeam1Player1Id()
    {
        return $this->team1Player1Id;
    }

    /**
     * Set status
     *
     * @param string $status
     * @return Game
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set team0Score
     *
     * @param integer $team0Score
     * @return Game
     */
    public function setTeam0Score($team0Score)
    {
        $this->team0Score = $team0Score;

        return $this;
    }

    /**
     * Get team0Score
     *
     * @return integer
     */
    public function getTeam0Score()
    {
        return $this->team0Score;
    }

    /**
     * Set team1Score
     *
     * @param integer $team1Score
     * @return Game
     */
    public function setTeam1Score($team1Score)
    {
        $this->team1Score = $team1Score;

        return $this;
    }

    /**
     * Get team1Score
     *
     * @return integer
     */
    public function getTeam1Score()
    {
        return $this->team1Score;
    }

    /**
     * @param $teamPosition
     * @param $teamId
     * @return $this
     */
    public function setTeamId($teamPosition, $teamId)
    {
        if ($teamPosition) {

            $this->setTeam1Id((int)$teamId);
        }
        else {

            $this->setTeam0Id((int)$teamId);
        }

        return $this;
    }

    /**
     * Set team0Id
     *
     * @param integer $team0Id
     * @return Game
     */
    public function setTeam0Id($team0Id)
    {
        $this->team0Id = $team0Id;

        return $this;
    }

    /**
     * Get team0Id
     *
     * @return integer
     */
    public function getTeam0Id()
    {
        return $this->team0Id;
    }

    /**
     * Set team1Id
     *
     * @param integer $team1Id
     * @return Game
     */
    public function setTeam1Id($team1Id)
    {
        $this->team1Id = $team1Id;

        return $this;
    }

    /**
     * Get team1Id
     *
     * @return integer
     */
    public function getTeam1Id()
    {
        return $this->team1Id;
    }

    /**
     * Set matchType
     *
     * @param string $matchType
     * @return Game
     */
    public function setMatchType($matchType)
    {
        $this->matchType = $matchType;

        return $this;
    }

    /**
     * Get matchType
     *
     * @return string
     */
    public function getMatchType()
    {
        return $this->matchType;
    }

    /**
     * @param $teamPosition
     * @return $this
     */
    public function addTeamScore($teamPosition)
    {
        if ((int) $teamPosition) {
            $this->setTeam1Score($this->getTeam1Score() + 1);
        } else {
            $this->setTeam0Score($this->getTeam0Score() + 1);
        }
        return $this;
    }

    public function getTeamScore($teamPosition)
    {
        if ((int) $teamPosition) {
            return $this->getTeam1Score();
        }
        return $this->getTeam0Score();
    }

    /**
     * Set contestId
     *
     * @param integer $contestId
     * @return Game
     */
    public function setContestId($contestId)
    {
        $this->contestId = $contestId;

        return $this;
    }

    /**
     * Get contestId
     *
     * @return integer
     */
    public function getContestId()
    {
        return $this->contestId;
    }

    /**
     * Set startedAt
     *
     * @param \DateTime $startedAt
     * @return Game
     */
    public function setStartedAt($startedAt)
    {
        $this->startedAt = $startedAt;

        return $this;
    }

    /**
     * Get startedAt
     *
     * @return \DateTime
     */
    public function getStartedAt()
    {
        return $this->startedAt;
    }

    /**
     * Set finishedAt
     *
     * @param \DateTime $finishedAt
     * @return Game
     */
    public function setFinishedAt($finishedAt)
    {
        $this->finishedAt = $finishedAt;

        return $this;
    }

    /**
     * Get finishedAt
     *
     * @return \DateTime
     */
    public function getFinishedAt()
    {
        return $this->finishedAt;
    }

    private function TeamAndPlayerMethod($teamPosition, $playerPosition, $action)
    {
        return sprintf('%sTeam%uPlayer%uId', $action, $teamPosition, $playerPosition);
    }
    /**
     * @param $teamPosition
     * @param $playerPosition
     * @param $playerId
     */
    public function setPlayer($teamPosition, $playerPosition, $playerId)
    {
        $method = $this->TeamAndPlayerMethod($teamPosition, $playerPosition, 'set');
        $this->$method($playerId);
    }

    /**
     * @param $playerId
     * @return bool
     */
    public function isPlayerInThisGame($playerId)
    {
        for ($teamPosition=0; $teamPosition <= 1; $teamPosition++) {

            for ($playerPosition=0; $playerPosition <= 1; $playerPosition++) {

                if ($user = $this->getPlayer($teamPosition, $playerPosition)) {

                    if ($user->getId() == (int)$playerId) {

                        return true;
                    }
                }
            }
        }

        return false;
    }

    /**
     * @param $player
     */
    public function setAllPlayers(\Indigo\UserBundle\Entity\User $player)
    {
        $this->setTeam0Player0Id($player);
        $this->setTeam0Player1Id($player);
        $this->setTeam1Player0Id($player);
        $this->setTeam1Player1Id($player);
    }

    /**
     * @param $teamPosition
     * @return int
     */
    public function getPlayersCountInTeam($teamPosition)
    {
        $playersInTeam = 0;
        for ($playerPosition=0; $playerPosition <= 1; $playerPosition++) {

            $user = $this->getPlayer($teamPosition, $playerPosition);
            if ($user && $user->getId() > 0 ) {

                $playersInTeam++;
            }
        }

        return $playersInTeam;
    }

    /**
     * @param $teamPosition
     * @param $playerPosition
     * @return mixed
     */
    public function getPlayer($teamPosition, $playerPosition) {

        $method = $this->TeamAndPlayerMethod((int) $teamPosition, (int) $playerPosition, 'get');

        return $this->$method();
    }

    /**
     * @return bool
     */
    public function isBothTeamReady() {
        return (bool)(($this->getPlayersCountInTeam(0) + $this->getPlayersCountInTeam(1)) == 4);
    }

    /**
     * @param $team
     * @return int
     */
    public function getMemberCountInTeam ($team)
    {
        if ((int)$team == 0) {
            return  ($this->getTeam0Player0Id() ? 1 : 0) +
            ($this->getTeam0Player1Id() ? 1 : 0);
        } else {
            return  ($this->getTeam1Player0Id() ? 1 : 0) +
            ($this->getTeam1Player1Id() ? 1 : 0);
        }
    }

}
