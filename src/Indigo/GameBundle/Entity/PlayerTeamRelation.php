<?php

namespace Indigo\GameBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PlayerTeamRelation
 *
 * @ORM\Table(name="players_teams")
 * @ORM\Entity(repositoryClass="Indigo\GameBundle\Entity\PlayerTeamRelationRepository")
 */
class PlayerTeamRelation
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
     * @var \Indigo\UserBundle\Entity\User
     * @ORM\ManyToOne(targetEntity="Indigo\UserBundle\Entity\User", inversedBy="teams", cascade={"persist"})
     * @ORM\JoinColumn(name="player_id", nullable=false)
     */
    private $player;

    /**
     * @var \Indigo\GameBundle\Entity\Team
     * @ORM\ManyToOne(targetEntity="Indigo\GameBundle\Entity\Team", inversedBy="players", cascade={"persist"})
     * @ORM\JoinColumn(name="team_id", nullable=false)
     */
    private $team;

    /**
     * @ORM\Column(name="team_id", type="integer")
     */
    private $teamId;


    /**
     * @return mixed
     */
    public function getTeamId()
    {
        return $this->teamId;
    }

    /**
     * @param mixed $teamId
     */
    public function setTeamId($teamId)
    {
        $this->teamId = $teamId;
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
     * @param \Indigo\UserBundle\Entity\User
     * @return $this
     */
    public function setPlayer(\Indigo\UserBundle\Entity\User $player)
    {
        $this->player = $player;
        $this->player->addTeam($this);

        return $this;
    }

    /**
     * @return \Indigo\UserBundle\Entity\User
     */
    public function getPlayer()
    {
        return $this->player;
    }

    /**
     * @param \Indigo\GameBundle\Entity\Team
     * @return $this
     */
    public function setTeam(\Indigo\GameBundle\Entity\Team $team)
    {
        $this->team = $team;
        $team->addPlayer($this);

        return $this;
    }

    /**
     * @return Team
     */
    public function getTeam()
    {
        return $this->team;
    }
}
