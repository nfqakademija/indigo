<?php

namespace Indigo\GameBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Indigo\ContestBundle\Entity\Contest;
/**
 * Team
 *
 * @ORM\Table(name="teams")
 * @ORM\Entity(repositoryClass="Indigo\GameBundle\Entity\TeamRepository")
 */
class Team
{
    const SINGLE_PLAYER_TEAM_NAME = 'singlePlayerTeam';
    const MULTI_PLAYER_TEAM_NAME = 'multiPlayerTeam';

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", options={"unsigned":true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var integer
     *
     * @ORM\Column(name="is_single", type="smallint", options={"unsigned":true})
     */
    private $isSingle;

    /**
     * @var ArrayCollection(<Rating>)
     * @ORM\OneToMany(targetEntity="Indigo\GameBundle\Entity\Rating", mappedBy="team")
     *
     */
    private $ratings;

    /**
     * @var ArrayCollection(<PlayerTeamRelation>)
     * @ORM\OneToMany(targetEntity="Indigo\GameBundle\Entity\PlayerTeamRelation", mappedBy="team", cascade={"persist"})
     */
    private $players;

    /**
     * @ORM\Column(name="contest_rating", type="integer", options={"unsigned":true})
     */
    private $contestRating;

    /**
     * @ORM\Column(name="open_rating", type="integer", options={"unsigned":true})
     */
    private $openRating;


    public function __construct()
    {
        $this->players = new ArrayCollection();
        $this->ratings = new ArrayCollection();
        $this
            ->setContestRating(TeamRepository::DEFAULT_RATING)
            ->setOpenRating(TeamRepository::DEFAULT_RATING);
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
    public function setRatings($ratings)
    {
        $this->ratings = $ratings;
    }



    /**
     * @return mixed
     */
    public function getPlayers()
    {
        return $this->players;
    }

    /**
     * @param $players
     * @return $this
     */
    public function setPlayers($players)
    {
        $this->players = $players;
        return $this;
    }

    /**
     * @param $player
     */
    public function addPlayer($player)
    {
        $this->players->add($player);
    }

    /**
     * @return int
     */
    public function getIsSingle()
    {
        return $this->isSingle;
    }

    /**
     * @param int $isSingle
     */
    public function setIsSingle($isSingle)
    {
        $this->isSingle = $isSingle;
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
     * Set name
     *
     * @param string $name
     * @return Team
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return mixed
     */
    public function getContestRating()
    {
        return $this->contestRating;
    }

    /**
     * @param $contestRating
     * @return $this
     */
    public function setContestRating($contestRating)
    {
        $this->contestRating = $contestRating;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getOpenRating()
    {
        return $this->openRating;
    }

    /**
     * @param $openRating
     * @return $this
     */
    public function setOpenRating($openRating)
    {
        $this->openRating = $openRating;
        return $this;
    }

    /**
     * @param $contestId
     * @return mixed
     */
    public function getTeamRatings($contestId)
    {
        if ((int) $contestId == Contest::OPEN_CONTEST_ID) {

            return $this->getOpenRating();
        }

        return $this->getContestRating();
    }

}
