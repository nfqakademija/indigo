<?php

namespace Indigo\GameBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * TableStatus
 *
 * @ORM\Table(name="tables_status")
 * @ORM\Entity(repositoryClass="Indigo\GameBundle\Entity\TableStatusRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class TableStatus
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
     * @var integer
     *
     * @ORM\Column(name="busy", type="integer", options={"unsigned"=true})
     */
    private $busy;

    /**
     * @var ArrayCollection<Game>
     * @ORM\OneToMany(targetEntity="Indigo\GameBundle\Entity\Game", mappedBy="tableStatus", cascade={"persist"})
     * @ORM\JoinColumn(name="game_id", referencedColumnName="id")
     */
    private $games;

    /**
     * @ORM\OneToOne(targetEntity="Indigo\GameBundle\Entity\Game",cascade={"persist"})
     * @ORM\JoinColumn(name="game_id", referencedColumnName="id", nullable=true)
     */
    private $game;

    /**
     * @ORM\Column(name="game_id", type="integer", nullable=true, options={"unsigned"=true})
     */
    private $gameId;

    /**
     * @var integer
     * @ORM\Column(name="table_id", type="integer", unique=true, options={"unsigned"=true})
     */
    private $tableId;

    /**
     * @var integer
     * @ORM\Column(name="last_swipe_ts", type="integer", options={"unsigned"=true})
     */
    private $lastSwipeTs;

    /**
     * @var integer
     * @ORM\Column(name="last_api_record_id", type="integer", options={"unsigned"=true})
     */
    private $lastApiRecordId;

    /**
     * @var integer
     * @ORM\Column(name="last_api_record_ts", type="integer", options={"unsigned"=true})
     */
    private $lastApiRecordTs;

    /**
     * @var integer
     * @ORM\Column(name="last_swiped_card_id", type="integer", options={"unsigned"=true})
     */
    private $lastSwipedCardId;

    /**
     * @var integer
     * @ORM\Column(name="last_tableshake_ts", type="integer", options={"unsigned"=true})
     */
    private $lastTableshakeTs;

    /**
     * @ORM\Column(name="last_update_ts", type="integer", options={"unsigned"=true})
     */
    private $lastUpdateTs;

    /**
     * @var string
     * @ORM\Column(name="url", type="string")
     */
    private $Url;


    public function __construct()
    {
        $this->games = new ArrayCollection();
    }

    /**
     * @ORM\PrePersist()
     */
    public function prePersist()
    {
        if ($this->getBusy() === null) {
            $this->setBusy(0);
        }

        if ($this->getLastSwipedCardId() === null) {
            $this->setLastSwipedCardId(0);
        }

        if ($this->getLastSwipeTs() === null) {
            $this->setLastSwipeTs(0);
        }

        if ($this->getLastTableshakeTs() === null) {
            $this->setLastTableshakeTs(0);
        }

    }

    /**
     * @return int
     */
    public function getLastApiRecordId()
    {
        return $this->lastApiRecordId;
    }

    /**
     * @param int $lastApiRecordId
     */
    public function setLastApiRecordId($lastApiRecordId)
    {
        $this->lastApiRecordId = $lastApiRecordId;
    }

    /**
     * @return int
     */
    public function getLastApiRecordTs()
    {
        return $this->lastApiRecordTs;
    }

    /**
     * @param int $lastApiRecordTs
     */
    public function setLastApiRecordTs($lastApiRecordTs)
    {
        $this->lastApiRecordTs = $lastApiRecordTs;
    }

    /**
     * Get id
     *
<<<<<<< HEAD
     * @return integer 
=======
     * @return integer
>>>>>>> api_test
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getTableId()
    {
        return $this->tableId;
    }

    /**
     * @param int $tableId
     */
    public function setTableId($tableId)
    {
        $this->tableId = $tableId;
    }

    /**
     * @return int
     */
    public function getLastSwipeTs()
    {
        return $this->lastSwipeTs;
    }

    /**
     * @param int $lastSwipeTs
     */
    public function setLastSwipeTs($lastSwipeTs)
    {
        $this->lastSwipeTs = $lastSwipeTs;
    }

    /**
     * @return int
     */
    public function getLastSwipedCardId()
    {
        return $this->lastSwipedCardId;
    }

    /**
     * @param int $lastSwipedCardId
     */
    public function setLastSwipedCardId($lastSwipedCardId)
    {
        $this->lastSwipedCardId = $lastSwipedCardId;
    }

    /**
     * @return int
     */
    public function getLastTableshakeTs()
    {
        return $this->lastTableshakeTs;
    }

    /**
     * @param int $lastTableshakeTs
     */
    public function setLastTableshakeTs($lastTableshakeTs)
    {
        $this->lastTableshakeTs = $lastTableshakeTs;
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
     * Set Games
     *
     * @param ArrayCollection $games
     * @return TableStatus
     */
    public function setGames(ArrayCollection $games)
    {
        $this->games = $games;
        return $this;
    }

    /**
     * @param Game $game
     * @return $this
     */
    public function addNewGame(Game $game)
    {
        $this->setGame($game);
        $this->games->add($game);
        $game->setTableStatus($this);
        return $this;
    }

    /**
     * Get Games
     *
     * @return integer
     */
    public function getGames()
    {
        return $this->games;
    }

/*
    public function getActiveGame()
    {
        foreach ($this->games as $game) {

            if (!$game->isGameStatusFinished()) {

                return $game;
            }
        }
        return null;
    }
*/

    /**
     * @return bool
     */
    public function hasPlayers() {

        if ($game = $this->getActiveGame()) {

            if ($game->getPlayersCount() >0) {

                return true;
            }
        }
        return false;
    }

    /**
     * @return mixed
     */
    public function getGame()
    {
        return $this->game;
    }

    /**
     * Gali buti ir null
     *
     * @param $game
     * @return $this
     */
    public function setGame($game)
    {
        $this->game = $game;
        if ($game instanceof Game) {
            $game->setTableStatus($this);
        }
        return $this;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->Url;
    }

    /**
     * @param string $Url
     */
    public function setUrl($Url)
    {
        $this->Url = $Url;
    }

    public function hasTimeout()
    {
        return  (bool) ($this->getLastUpdateTs() + TableStatusRepository::TIMEOUT < time());
    }

    /**
     * @return mixed
     */
    public function getGameId()
    {
        return $this->gameId;
    }

    /**
     * @param mixed $gameId
     */
    public function setGameId($gameId)
    {
        $this->gameId = (int)$gameId;
    }

    /**
     * @return mixed
     */
    public function getCurrentGame()
    {
        return $this->currentGame;
    }

    /**
     * @param mixed $currentGame
     */
    public function setCurrentGame($currentGame)
    {
        $this->currentGame = $currentGame;
    }

    /**
     * @return mixed
     */
    public function getLastUpdateTs()
    {
        return $this->lastUpdateTs;
    }

    /**
     * @param mixed $lastUpdateTs
     */
    public function setLastUpdateTs($lastUpdateTs)
    {
        $this->lastUpdateTs = $lastUpdateTs;
    }
}
