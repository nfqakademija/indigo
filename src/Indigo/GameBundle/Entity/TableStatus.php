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
     * @ORM\Column(name="id", type="integer")
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
     * @ORM\JoinColumn(name="games_id", referencedColumnName="id", nullable=true)
     */
    private $games;

    /**
     * @ORM\OneToOne(targetEntity="Indigo\GameBundle\Entity\Game", mappedBy="tableStatus", cascade={"persist"})
     * @ORM\JoinColumn(name="game_id", referencedColumnName="id", nullable=true)
     */
    private $game;

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
     * @return integer
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
     */
    public function setGame($game)
    {
        $this->game = $game;
    }



}
