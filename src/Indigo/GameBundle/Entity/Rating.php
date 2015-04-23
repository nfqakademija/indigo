<?php

namespace Indigo\GameBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Rating
 *
 * @ORM\Table(name="ratings")
 * @ORM\Entity(repositoryClass="Indigo\GameBundle\Entity\RatingRepository")
 */
class Rating
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
     * @var \Indigo\GameBundle\Entity\Team
     * @ORM\ManyToOne(targetEntity="Indigo\GameBundle\Entity\Team", inversedBy="ratings", cascade={"persist"})
     * @ORM\JoinColumn(name="team_id", referencedColumnName="id")
     */
    private $team;

    /**
     * @var \Indigo\GameBundle\Entity\Game
     * @ORM\ManyToOne(targetEntity="Indigo\GameBundle\Entity\Game", inversedBy="ratings")
     * @ORM\JoinColumn(name="game_id", referencedColumnName="id")
     */
    private $game;

    /**
     * @var integer
     *
     * @ORM\Column(name="rating", type="integer", nullable=false)
     */
    private $rating;

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
     * @param Team $team
     * @return $this
     */
    public function setTeam(Team $team)
    {
        $this->team = $team;

        return $this;
    }

    /**
     * Get team
     *
     * @return integer 
     */
    public function getTeam()
    {
        return $this->team;
    }

    /**
     * @param Game $game
     * @return $this
     */
    public function setGame(Game $game)
    {
        $this->game = $game;
        $this->getGame()->addRating($this);
        return $this;
    }

    /**
     * Get game
     *
     * @return integer 
     */
    public function getGame()
    {
        return $this->game;
    }

    /**
     * Set rating
     *
     * @param integer $rating
     * @return Rating
     */
    public function setRating($rating)
    {
        $this->rating = $rating;

        return $this;
    }

    /**
     * Get rating
     *
     * @return integer 
     */
    public function getRating()
    {
        return $this->rating;
    }
}
