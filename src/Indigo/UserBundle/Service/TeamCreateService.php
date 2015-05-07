<?php

namespace Indigo\UserBundle\Service;


use Doctrine\ORM\EntityManagerInterface;
use Indigo\GameBundle\Entity\Team;
use Indigo\UserBundle\Entity\User;
use Proxies\__CG__\Indigo\GameBundle\Entity\PlayerTeamRelation;

class TeamCreateService
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @param EntityManagerInterface $em
     */
    public function __construct (EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param User $player0
     * @param User $player1
     * @return Team
     */
    public function createMultiPlayerTeam(User $player0, User $player1)
    {
        $teamEntity = $this->createTeam(false, Team::MULTI_PLAYER_TEAM_NAME);
        $playerToTeamRelation1 = $this->createPlayerTeamRelation($player0, $teamEntity);
        $playerToTeamRelation2 = $this->createPlayerTeamRelation($player1, $teamEntity);
        $this->em->persist($playerToTeamRelation1);
        $this->em->persist($playerToTeamRelation2);

        return $teamEntity;
    }

    /**
     * @param User $player
     * @return Team
     */
    public function createSinglePlayerTeam(User $player)
    {
        $teamEntity = $this->createTeam(true, Team::SINGLE_PLAYER_TEAM_NAME);
        $playerToTeamRelation = $this->createPlayerTeamRelation($player, $teamEntity);
        $this->em->persist($playerToTeamRelation);

        return $playerToTeamRelation;
    }

    /**
     * @param $single
     * @param $name
     * @return Team
     */
    private function createTeam($single, $name)
    {
        $teamEntity = new Team();
        $teamEntity->setIsSingle((int) $single);
        $teamEntity->setName($name);

        return $teamEntity;
    }

    /**
     * @param User $player
     * @param Team $team
     * @return PlayerTeamRelation
     */
    private function createPlayerTeamRelation(User $player, Team $team)
    {
        $playerTeamRelationEntity = new PlayerTeamRelation();
        $playerTeamRelationEntity
            ->setPlayer($player)
            ->setTeam($team);

        return $playerTeamRelationEntity;
    }
}