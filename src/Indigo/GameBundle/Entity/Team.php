<?php

namespace Indigo\GameBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Team
 *
 * @ORM\Table(name="teams")
 * @ORM\Entity(repositoryClass="Indigo\GameBundle\Entity\TeamRepository")
 */
class Team
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
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var integer
     *
     * @ORM\Column(name="total_rating", type="integer")
     */
    private $totalRating;

    /**
     * @var integer
     *
     * @ORM\Column(name="is_single", type="integer")
     */
    private $isSingle;


    /**
     * @var ArrayCollection(<PlayerTeamRelation>)
     * @ORM\OneToMany(targetEntity="Indigo\GameBundle\Entity\PlayerTeamRelation", mappedBy="team", cascade={"persist"})
     */
    private $players;



    public function __construct()
    {
        $this->players = new ArrayCollection();
        $this->totalRating = 0;
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
     * Set totalRating
     *
     * @param integer $totalRating
     * @return Team
     */
    public function setTotalRating($totalRating)
    {
        $this->totalRating = $totalRating;

        return $this;
    }

    /**
     * Get totalRating
     *
     * @return integer 
     */
    public function getTotalRating()
    {
        return $this->totalRating;
    }
}
