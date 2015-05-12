<?php

namespace Indigo\GameBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Indigo\ContestBundle\Entity\Contest;
use Indigo\GameBundle\Repository\GameStatusRepository;
use Indigo\GameBundle\Repository\GameTypeRepository;
use Indigo\UserBundle\Entity\User;

/**
 * Game
 *
 * @ORM\Table(name="games")
 * @ORM\Entity(repositoryClass="Indigo\GameBundle\Entity\GameRepository")
 * @ORM\EntityListeners({"Indigo\GameBundle\EventListener\GamePrePersistListener"})
 */
class Game
{
    const DEFAULT_SCORE = 0;
    const GAME_WITH_STATS = 1;
    const GAME_WITHOUT_STATS = 0;
    const STATE_WIN = 1;
    const STATE_LOSE = 0;
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", options={"unsigned":true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var \Indigo\GameBundle\Entity\TableStatus
     * @ORM\ManyToOne(targetEntity="Indigo\GameBundle\Entity\TableStatus", inversedBy="games")
     * @ORM\JoinColumn(name="table_status_id", referencedColumnName="id")
     */
    private $tableStatus;


    /**
     * @ORM\Column(name="table_status_id", type="integer", options={"unsigned":true})
     */
    private $tableStatusId;

    /**
     * @var \Indigo\GameBundle\Entity\GameTime
     * @ORM\ManyToOne(targetEntity="Indigo\GameBundle\Entity\GameTime", inversedBy="games", cascade={"persist"})
     * @ORM\JoinColumn(name="game_time", referencedColumnName="id")
     */
    private $gameTime;

    /**
     * @ORM\Column(name="game_time", type="integer", nullable=true, options={"unsigned":true})
     */
    private $gameTimeId;

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
     * @ORM\Column(name="team0_score", type="smallint", options={"default"=0, "unsigned":true})
     */
    private $team0Score;

    /**
     * @var integer
     *
     * @ORM\Column(name="team1_score", type="smallint", options={"default"=0, "unsigned":true})
     */
    private $team1Score;

    /**
     * @var \Indigo\GameBundle\Entity\Team
     * @ORM\ManyToOne(targetEntity="Indigo\GameBundle\Entity\Team")
     * @ORM\JoinColumn(name="team0_id", referencedColumnName="id")
     */
    private $team0;

    /**
     * @var \Indigo\GameBundle\Entity\Team
     * @ORM\ManyToOne(targetEntity="Indigo\GameBundle\Entity\Team")
     * @ORM\JoinColumn(name="team1_id", referencedColumnName="id")
     */
    private $team1;

    /**
     * @var \Indigo\GameBundle\Entity\Team
     * @ORM\ManyToOne(targetEntity="Indigo\GameBundle\Entity\Team")
     * @ORM\JoinColumn(name="team_won", referencedColumnName="id")
     */
    private $teamWon;

    /**
     * @var string
     *
     * @ORM\Column(name="match_type", type="string")
     * columnDefinition="ENUM('single', 'team', 'open')"
     */
    private $matchType;

    /**
     * @ORM\ManyToOne(targetEntity="Indigo\ContestBundle\Entity\Contest", inversedBy="games")
     * @ORM\JoinColumn(name="contest_id", referencedColumnName="id")
     */
    private $contest;

    /**
     * @ORM\Column(name="contest_id", type="integer", options={"unsigned":true})
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

    /**
     * @var ArrayCollection<Rating>
     * @ORM\OneToMany(targetEntity="Indigo\GameBundle\Entity\Rating", mappedBy="game" )
     */
    private $ratings;

    /**
     * @ORM\Column(name="is_stat", type="smallint", nullable=false, options={"default": 0})
     */
    private $isStat;


    public function __construct()
    {
        $this->ratings = new ArrayCollection();

        $this->setTeam0Score(self::DEFAULT_SCORE);
        $this->setTeam1Score(self::DEFAULT_SCORE);

        $this->isStat = self::GAME_WITHOUT_STATS;

        $this->setMatchType(GameTypeRepository::TYPE_GAME_OPEN);
        $this->setStatus(GameStatusRepository::STATUS_GAME_WAITING);
    }

    /**
     * @param $teamPosition
     * @param $playerPosition
     * @param $action
     * @return string
     */
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
     * @param $teamPosition
     * @param $playerPosition
     * @return mixed
     */
    public function getPlayer($teamPosition, $playerPosition)
    {

        $method = $this->TeamAndPlayerMethod((int) $teamPosition, (int) $playerPosition, 'get');

        $user = $this->$method();
        if ($user && $user->getId() > 0) {

            return $user;
        }

        return null;
    }

    /**
     * @return \ArrayIterator
     */
    public function getAllPlayers()
    {
        $players = new \ArrayIterator();
        for ($teamPosition = 0; $teamPosition <= 1; $teamPosition++) {

            for ($playerPosition = 0; $playerPosition <= 1; $playerPosition++) {

                if ($player = $this->getPlayer($teamPosition, $playerPosition)) {
                    $players->append($player);
                }
            }
        }

        return $players;
    }

    public function getPlayersInTeam($teamPosition)
    {
        $players = new \ArrayIterator();
        for ($playerPosition = 0; $playerPosition <= 1; $playerPosition++) {

            if ($player = $this->getPlayer($teamPosition, $playerPosition)) {

                $players->append($player);
            }
        }

        return $players;
    }

    /**
     * @param $teamPosition
     * @return int
     */
    public function getPlayersCountInTeam($teamPosition)
    {
        $players = $this->getPlayersInTeam($teamPosition);

        return $players->count();
    }

    /**
     * @return int
     */
    public function getPlayersCount()
    {
        return $this->getPlayersCountInTeam(0) + $this->getPlayersCountInTeam(1);
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
     * @param $teamPosition
     * @param Team $team
     * @return $this
     */
    public function setTeam($teamPosition, Team $team)
    {
        if ($teamPosition) {

            $this->setTeam1($team);
        }
        else {

            $this->setTeam0($team);
        }

        return $this;
    }

    /**
     * @param $teamPosition
     * @return Team
     */
    public function getTeam($teamPosition)
    {
        if ($teamPosition) {

            return $this->getTeam1();
        }

        return $this->getTeam0();
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

    /**
     * @param $teamPosition
     * @return int
     */
    public function getTeamScore($teamPosition)
    {
        if ((int) $teamPosition) {
            return $this->getTeam1Score();
        }
        return $this->getTeam0Score();
    }

    /**
     * @return bool
     */
    public function hasBothTeamsAllPlayers()
    {
        return (bool)(($this->getPlayersCount()) == 4);
    }

    /**
     * @return bool
     */
    public function isInGameBothTeams()
    {
        return (bool)($this->getPlayersCountInTeam(0) > 0 && $this->getPlayersCountInTeam(1) > 0);
    }

    /**
     * Turi buti bent du zaidejai
     *
     * @return bool
     */
    public function isEvenPlayersCount()
    {
        return (bool)($this->isInGameBothTeams() && $this->getPlayersCount() % 2 == 0);
    }

    /**
     * @return int
     */
    public function getWinnerTeamPosition()
    {
        if ($this->getTeam0Score() == $this->getTeam1Score()) {
            return -1;
        }

        return  ($this->getTeam0Score() > $this->getTeam1Score() ? 0 : 1);
    }
    /**
     * @return bool
     */
    public function isGameStatusStarted()
    {
        return (bool)($this->getStatus() == GameStatusRepository::STATUS_GAME_STARTED);
    }

    /**
     * @return bool
     */
    public function isGameStatusFinished()
    {
        return (bool)($this->getStatus() == GameStatusRepository::STATUS_GAME_FINISHED);
    }

    /**
     * @return bool
     */
    public function isGameStatusReady()
    {
        return (bool)($this->getStatus() == GameStatusRepository::STATUS_GAME_READY);
    }

    /**
     * @return bool
     */
    public function isGameStatusWaiting()
    {
        return (bool)($this->getStatus() == GameStatusRepository::STATUS_GAME_READY);
    }

    /**
     *
     */
    public function getDuration()
    {
        if ($this->getStatus() == GameStatusRepository::STATUS_GAME_FINISHED) {

            return abs($this->getFinishedAt()->getTimestamp() - $this->getStartedAt()->getTimestamp());
        }
    }

    /**
     * @param Rating $rating
     */
    public function addRating(Rating $rating)
    {
        $this->ratings->add($rating);
    }









    /**
     * @return ArrayCollection
     */
    public function getRatings()
    {
        return $this->ratings;
    }

    /**
     * @param ArrayCollection $ratings
     */
    public function setRatings(ArrayCollection $ratings)
    {
        $this->ratings = $ratings;
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
     * Set gametime
     * could be null
     * @param \Indigo\GameBundle\Entity\GameTime $gameTime
     * @return Game
     */
    public function setGameTime($gameTime)
    {
        $this->gameTime = $gameTime;

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
     * @param Team $team0
     * @return $this
     */
    public function setTeam0(Team $team0)
    {
        $this->team0 = $team0;

        return $this;
    }

    /**
     * Get team0
     *
     * @return Team
     */
    public function getTeam0()
    {
        return $this->team0;
    }

    /**
     * @param Team $team1
     * @return $this
     */
    public function setTeam1(Team $team1)
    {
        $this->team1 = $team1;

        return $this;
    }

    /**
     * Get team1
     *
     * @return Team
     */
    public function getTeam1()
    {
        return $this->team1;
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
     * Set contest
     *
     * @param Contest $contest
     * @return Game
     */
    public function setContest(Contest $contest)
    {
        $this->contest = $contest;

        return $this;
    }

    /**
     * Get contest
     *
     * @return integer
     */
    public function getContest()
    {
        return $this->contest;
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
     * @return mixed
     */
    public function getGameTimeId()
    {
        return $this->gameTimeId;
    }

    /**
     * @param mixed $gameTimeId
     */
    public function setGameTimeId($gameTimeId)
    {
        $this->gameTimeId = $gameTimeId;
    }

    /**
     * @return mixed
     */
    public function getIsStat()
    {
        return $this->isStat;
    }

    /**
     * @param $isStat
     * @return $this
     */
    public function setIsStat($isStat)
    {
        $this->isStat = (int)$isStat;
        return $this;
    }

    /**
     * @return Team
     */
    public function getTeamWon()
    {
        return $this->teamWon;
    }

    /**
     * @return Team
     */
    public function getTeamLoss()
    {
        if ($this->getTeam0() == $this->getTeamWon()) {

            return $this->getTeam0;
        }

        return $this->getTeam1;
    }

    /**
     * @param $teamWon
     * @return $this
     */
    public function setTeamWon($teamWon)
    {
        $this->teamWon = $teamWon;
        return $this;
    }

    /**
     * @param User $userEntity
     * @return int
     */
    public function getPlayerTeamPosition(User $userEntity)
    {
        if ($this->getTeam0Player0Id() == $userEntity ||
            $this->getTeam0Player1Id() == $userEntity) {

            return 0;
        }

        return 1;
    }

    /**
     * @param $teamPosition
     * @param int $byScore
     */
    public function addTeamScores($teamPosition, $byScore = 1)
    {
        if ($teamPosition) {

            if ($this->getTeam1Score()) {

                $this->setTeam1Score($this->getTeam1Score() + $byScore);
            }
        } else {

            if ($this->getTeam0Score()) {

                $this->setTeam0Score($this->getTeam0Score() + $byScore);
            }
        }
    }
}
