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
use Indigo\UIBundle\Models\DashboardViewModel;
use Indigo\UIBundle\Models\LiveViewModel;
use Indigo\UIBundle\Models\PlayerModel;
use Indigo\UIBundle\Models\TeamModel;
use Symfony\Component\Security\Acl\Exception\Exception;


/**
 * Class IndigoService
 * @package Indigo\UIBundle\Services
 * @doctrine.orm.entity_manager
 */

class LiveViewService
{
    private $em;

    /**
     * @param \Doctrine\ORM\EntityManager $em
     */
    function __construct(\Doctrine\ORM\EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * @return array
     */
    public function getDashboardViewModel()
    {
        $dashModel = new DashboardViewModel();

        $dashModel->getCurrentContest()->setTitle("Vasaros turnyras");
        $dashModel->getCurrentContest()->setDescription("Laimėkite dviejų porų kelionę į Turkiją!");
        $dashModel->getCurrentContest()->setImageUrl("/bundles/indigoui/images/content-box.png");

        $dashModel->getNextContest()->setTitle("Greičio turnyras");
        $dashModel->getNextContest()->setDescription("Laimėkite pasivažinėjimą kartingais!");
        $dashModel->getNextContest()->setImageUrl("/bundles/indigoui/images/content-box-2.png");

        return $dashModel;
    }

    /**
     * @param $tableId
     * @return LiveViewModel
     */
    public function getTableStatus($tableId)
    {
        $model = new LiveViewModel();
        $contest = new ContestModel();
        $teamA = new TeamModel();
        $teamB = new TeamModel();
        $player00 = new PlayerModel("/bundles/indigoui/images/empty.png");
        $player01 = new PlayerModel("/bundles/indigoui/images/empty.png");
        $player10 = new PlayerModel("/bundles/indigoui/images/empty.png");
        $player11 = new PlayerModel("/bundles/indigoui/images/empty.png");

        $model->setReservations($this->getLastReservations());

        $model->setStatusMessage("Sveiki!");

        $tableStatus = $this->em->getRepository('IndigoGameBundle:TableStatus')->findOneById($tableId);

        if($tableStatus)
        {
            $model->setIsTableBusy($tableStatus->getBusy());
            if($tableStatus->getGame())
            {
                $gameEntity = $this->em->getRepository('IndigoGameBundle:Game')->find($tableStatus->getGame());


                if($gameEntity && ($gameEntity->getStatus() == "started" || $gameEntity->getStatus() == "waiting"))
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

                    $model->setStatusMessage("Žaidimas pradėtas!");
                }
                else
                {
                    $model->setStatusMessage("Registruokitės žaidimui!");
                }
            }
            else
            {
                $model->setStatusMessage("Registruokitės žaidimui!");
            }

            $teamA->setPlayer1( $player00 );
            $teamA->setPlayer2( $player01 );

            $teamB->setPlayer1( $player10 );
            $teamB->setPlayer2( $player11 );

            $model->setTeamA( $teamA );
            $model->setTeamB( $teamB );
        }
        else
        {
            $model->setStatusMessage("Uups... sistema nedirba.");
        }

        return $model;
    }

    /**
     * @param int
     * @param int
     * @return array
     */
    public function getLastReservations()
    {
        $reservations = array();

                $reservations [] = [
                  "time" => "12:00 - 12:15",
                  "status" => "Free",
                  "whoIsReservedName" => null,
                  "whoIsReservedImageUrl" => null
                ];

                $reservations [] = [
                    "time" => "12:15 - 12:30",
                    "status" => "Reserved",
                    "whoIsReservedName" => "Tadas",
                    "whoIsReservedImageUrl" => "/bundles/indigoui/images/tadas_surgailis.png"
                ];

                $reservations [] = [
                    "time" => "12:30 - 12:45",
                    "status" => "Free",
                    "whoIsReservedName" => null,
                    "whoIsReservedImageUrl" => null
                ];

        return $reservations;
    }

    public function getCurrentContestInfo()
    {
        return new Exception("This method is not implemented");
    }
}