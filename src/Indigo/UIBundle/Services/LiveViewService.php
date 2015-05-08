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
    private $defaultProfilePic = '';

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
        $player00 = new PlayerModel();
        $player01 = new PlayerModel();
        $player10 = new PlayerModel();
        $player11 = new PlayerModel();

        $model->setReservations($this->getLastReservations());

        $model->setStatusMessage("Sveiki!");

        $tableStatus = $this->em->getRepository('IndigoGameBundle:TableStatus')->findOneById($tableId);

        if($tableStatus)
        {
            $model->setIsTableBusy($tableStatus->getBusy());
            if($tableStatus->getGame())
            {
                if($tableStatus->getGame() && ($tableStatus->getGame()->getStatus() == "started" || $tableStatus->getGame()->getStatus() == "waiting" || $tableStatus->getGame()->getStatus() == "ready"))
                {
                    $contestEntity = $this->em->getRepository('IndigoContestBundle:Contest')->findOneById($tableStatus->getGame()->getContestId());

                    if($contestEntity)
                    {
                        $contest->setId($contestEntity->getId());
                        $contest->setTitle($contestEntity->getContestTitle());
                        //$contest->setDescription($contestEntity->getContestDescription());
                        $contest->getStartDate($contestEntity->getContestStartingDate());
                        $contest->getEndData($contestEntity->getContestEndDate());
                        $model->setContest($contest);
                    }

                    if($tableStatus->getGame()->getTeam0Player0Id()) {
                        $player00->setName($tableStatus->getGame()->getTeam0Player0Id()->getUsername());
                        $player00->setImageUrl($tableStatus->getGame()->getTeam0Player0Id()->getPicture());
                    }

                    if($tableStatus->getGame()->getTeam0Player1Id())
                    {
                        $player01->setName($tableStatus->getGame()->getTeam0Player1Id()->getUsername());
                        $player01->setImageUrl($tableStatus->getGame()->getTeam0Player1Id()->getPicture());
                    }

                    if($tableStatus->getGame()->getTeam1Player0Id())
                    {
                        $player10->setName($tableStatus->getGame()->getTeam1Player0Id()->getUsername());
                        $player10->setImageUrl($tableStatus->getGame()->getTeam1Player0Id()->getPicture());
                    }

                    if($tableStatus->getGame()->getTeam1Player1Id())
                    {
                        $player11->setName($tableStatus->getGame()->getTeam1Player1Id()->getUsername());
                        $player11->setImageUrl($tableStatus->getGame()->getTeam1Player1Id()->getPicture());
                    }

                    $teamA->setGoals($tableStatus->getGame()->getTeam0Score());
                    $teamB->setGoals($tableStatus->getGame()->getTeam1Score());

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