<?php

namespace Indigo\GameBundle\Service;


use Doctrine\ORM\EntityManagerInterface;
use Indigo\GameBundle\Entity\PlayerTeamRelation;
use Indigo\GameBundle\Entity\Team;
use Indigo\UserBundle\Entity\User;


class TeamCreate
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
        $teamEntity = $this->createTeam(Team::MULTI_PLAYER_TYPE, Team::MULTI_PLAYER_TEAM_NAME);
        $playerToTeamRelation0 = $this->createPlayerTeamRelation($player0, $teamEntity);
        $playerToTeamRelation1 = $this->createPlayerTeamRelation($player1, $teamEntity);
        $player0->addTeam($playerToTeamRelation0);
        $player1->addTeam($playerToTeamRelation1);

        $this->em->persist($playerToTeamRelation0);
        $this->em->persist($playerToTeamRelation1);

        return $teamEntity;
    }

    /**
     * @param User $player
     * @return Team
     */
    public function createSinglePlayerTeam(User $player)
    {
        $teamEntity = $this->createTeam(Team::SINGLE_PLAYER_TYPE, Team::SINGLE_PLAYER_TEAM_NAME);
        $playerToTeamRelation = $this->createPlayerTeamRelation($player, $teamEntity);
        $player->addTeam($playerToTeamRelation);
        $this->em->persist($playerToTeamRelation);

        return $teamEntity;
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