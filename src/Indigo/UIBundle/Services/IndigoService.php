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
            $gameEntity = $this->em->getRepository('IndigoGameBundle:Game')->find($tableStatus->getGame());

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
            }

            $playerA1 = new PlayerModel();
            $playerA1->setName("Dalia");
            //$playerA1->setImageUrl("/bundles/indigoui/images/dalia.png");

            $playerA2 = new PlayerModel();
            $playerA2->setName("Bilas");
            //$playerA2->setImageUrl("/bundles/indigoui/images/bilas.png");

            $playerB1 = new PlayerModel();
            $playerB1->setName("Tadas");
            //$playerB1->setImageUrl("/bundles/indigoui/images/tadas_surgailis.png");

            $playerB2 = new PlayerModel();
            $playerB2->setName("Klintonas");
            //$playerB2->setImageUrl("/bundles/indigoui/images/clinton.png");

            $teamA = new TeamModel();
            $teamA->setPlayer1( $playerA1 );
            $teamA->setPlayer2( $playerA2 );
            $teamA->setGoals(5);

            $teamB = new TeamModel();
            $teamB->setPlayer1( $playerB1 );
            $teamB->setPlayer2( $playerB2 );
            $teamB->setGoals(3);

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