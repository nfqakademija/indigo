<?php
/**
 * Created by PhpStorm.
 * User: TSU
 * Date: 2015.04.20
 * Time: 19:49
 */



namespace Indigo\UIBundle\Services;

use Indigo\GameBundle\Entity\Game;
use Indigo\GameBundle\Entity\Contest;
use Indigo\UIBundle\Models\ContestModel;
use Indigo\UIBundle\Models\LiveViewModel;
use Indigo\UIBundle\Models\PlayerModel;
use Indigo\UIBundle\Models\TeamModel;
use Symfony\Component\Security\Acl\Exception\Exception;


/**
 * Class IndigoService
 * @package Indigo\UIBundle\Services
 * @doctrine.orm.entity_manager
 */

class IndigoService
{
    private $em;

    /**
     * @param \Doctrine\ORM\EntityManager $em
     */
    function __construct(\Doctrine\ORM\EntityManager $em)
    {
        $this->em = $em;
    }

    public function getTableStatus($tableId)
    {
        $model = new LiveViewModel();
        $contest = new ContestModel();

        $tableStatus = $this->em->getRepository('IndigoGameBundle:TableStatus')->findOneById($tableId);

        if($tableStatus)
        {
            $model->setIsTableBusy($tableStatus->getBusy());
            if($tableStatus->getGame()) {
                $gameEntity = $this->em->getRepository('IndigoGameBundle:Game')->find($tableStatus->getGame());
            }
            else{
                $gameEntity = null;
            }

            $player00 = new PlayerModel();
            $player01 = new PlayerModel();
            $player10 = new PlayerModel();
            $player11 = new PlayerModel();

            $teamA = new TeamModel();
            $teamB = new TeamModel();

            if($gameEntity)
            {
                $contestEntity = $this->em->getRepository('IndigoContestBundle:Contest')->findOneById($gameEntity->getContestId());

                if($contestEntity)
                {
                    $contest->setId($contestEntity->getId());
                    $contest->setTitle($contestEntity->getContestTitle());
                    //$contest->setDescription($contestEntity->getContestDescription());
                    $contest->getStartDate($contestEntity->getContestStartingDate());
                    $contest->getEndData($contestEntity->getContestEndDate());
                    $model->setContest($contest);
                }

                $user00 = $this->em->getRepository('IndigoUserBundle:User')->findOneById($gameEntity->getTeam0Player0Id());

                if(!$user00)
                {
                    $user00 = $this->em->getRepository('IndigoUserBundle:User')->findOneById(0);
                }

                $user01 = $this->em->getRepository('IndigoUserBundle:User')->findOneById($gameEntity->getTeam0Player1Id());

                if(!$user01)
                {
                    $user01 = $this->em->getRepository('IndigoUserBundle:User')->findOneById(0);
                }

                $user10 = $this->em->getRepository('IndigoUserBundle:User')->findOneById($gameEntity->getTeam1Player0Id());

                if(!$user10)
                {
                    $user10 = $this->em->getRepository('IndigoUserBundle:User')->findOneById(0);
                }

                $user11 = $this->em->getRepository('IndigoUserBundle:User')->findOneById($gameEntity->getTeam1Player1Id());

                if(!$user11)
                {
                    $user11 = $this->em->getRepository('IndigoUserBundle:User')->findOneById(0);
                }

                $player00->setName($user00->getUsername());
                $player00->setImageUrl($user00->getPicture());
                $player01->setName($user01->getUsername());
                $player01->setImageUrl($user01->getPicture());
                $player10->setName($user10->getUsername());
                $player10->setImageUrl($user10->getPicture());
                $player11->setName($user11->getUsername());
                $player11->setImageUrl($user11->getPicture());

                $teamA->setGoals($gameEntity->getTeam0Score());
                $teamB->setGoals($gameEntity->getTeam1Score());
            }


            $teamA->setPlayer1( $player00 );
            $teamA->setPlayer2( $player01 );

            $teamB->setPlayer1( $player10 );
            $teamB->setPlayer2( $player11 );

            $model->setTeamA( $teamA );
            $model->setTeamB( $teamB );
        }
        return $model;
    }

    public function getCurrentContestInfo()
    {
        return new Exception("This method is not implemented");
    }
}