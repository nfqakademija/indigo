<?php
/**
 * Created by PhpStorm.
 * User: TSU
 * Date: 2015.04.20
 * Time: 19:49
 */



namespace Indigo\UIBundle\Services;

use Doctrine\ORM\EntityManagerInterface;
use Indigo\GameBundle\Entity\Contest;
use Indigo\GameBundle\Entity\Game;
use Indigo\UIBundle\Models\ContestModel;
use Indigo\UIBundle\Models\DashboardViewModel;
use Indigo\UIBundle\Models\LiveViewModel;
use Indigo\UIBundle\Models\PlayerModel;
use Indigo\UIBundle\Models\TeamModel;
use Indigo\UIBundle\Models\WinnerTeamModel;
use Symfony\Component\Security\Acl\Exception\Exception;


/**
 * Class IndigoService
 * @package Indigo\UIBundle\Services
 * @doctrine.orm.entity_manager
 */

class LiveViewService
{
    private $defRegistrationTimeInterval = 15;
    private $defaultProfilePic = '';

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @param EntityManagerInterface $em
     */
    function __construct(EntityManagerInterface $em)
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

        $lastWinner = $this->getLastWinnerModel(60);
        if($lastWinner)
        {
            $model->setShowGreetingMessage(true);
            $model->setLastWinnerTeam($lastWinner);
        }

       // $model->setShowGreetingMessage( $this->doWeHaveWinner(10) );

        $tableStatus = $this->em->getRepository('IndigoGameBundle:TableStatus')->findOneById($tableId);

        if($tableStatus)
        {
            $model->setIsTableBusy($tableStatus->getBusy());
            if($tableStatus->getGame())
            {
                if($tableStatus->getGame() && ($tableStatus->getGame()->getStatus() == "started" || $tableStatus->getGame()->getStatus() == "waiting" || $tableStatus->getGame()->getStatus() == "ready"))
                {
                    $contestEntity = $tableStatus->getGame()->getContest();
                    //$this->em->getRepository('IndigoContestBundle:Contest')->findOneById($tableStatus->getGame()->getContestId());

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
        $segment = floor( date("i") / $this->defRegistrationTimeInterval);
        $dateFrom = new \DateTime(date("Y-m-d H:0:0"));
        $dateFrom->add(new \DateInterval('PT' . $segment * $this->defRegistrationTimeInterval. 'M'));
        $dateTo = new \DateTime($dateFrom->format('Y-m-d H:i:s'));
        $dateTo->add(new \DateInterval('PT' . $this->defRegistrationTimeInterval . 'M'));

        $now =  new \DateTime(date("Y-m-d H:00:00"));
        $now->add(new \DateInterval('PT' . $segment * $this->defRegistrationTimeInterval . 'M'));
        $reservations = array();

                for($i = 0; $i < 3; $i++ )
                {
                    $reservation = $this->getReservation($now);

                    if($reservation)
                    {
                        $id = $reservation[0]["timeOwner"];
                        $user = $this->getUserById($id);

                        $reservations [] = [
                            "time" => $dateFrom->format('H:i') .'-'. $dateTo->format('H:i'),
                            "status" => "Reserved",
                            "whoIsReservedName" => $user != null ? $user->getUsername() : null,
                            "whoIsReservedImageUrl" => $user != null ? $user->getPicture() : null
                        ];
                    }
                    else
                    {
                        $reservations [] = [
                            "time" => $dateFrom->format('H:i') .'-'. $dateTo->format('H:i'),
                            "status" => "Free",
                            "whoIsReservedName" => null,
                            "whoIsReservedImageUrl" => null
                        ];
                    }

                    $now->add(new \DateInterval('PT' . $this->defRegistrationTimeInterval . 'M'));

                    $dateFrom = $dateTo;
                    $dateTo = new \DateTime($dateTo->format('Y-m-d H:i:s'));
                    $dateTo->add(new \DateInterval('PT' . $this->defRegistrationTimeInterval . 'M'));
                }
        return $reservations;
    }

    public function getCurrentContestInfo()
    {
        return new Exception("This method is not implemented");
    }

    /**
     * @param $time
     * @return array
     */
    public function getReservation($time)
    {
        $qb = $this->em->getRepository('IndigoGameBundle:GameTime')->createQueryBuilder('p');
        $qb->where('p.startAt = :time')
            ->andWhere('p.finishAt >  :time')
            ->setParameter('time', $time)
            ->orderBy('p.finishAt', 'DESC');

        return $qb->getQuery()->getArrayResult();
    }

    /**
     * @param int
     * @return \Indigo\UserBundle\Entity\User
     */
    public function getUserById($userId)
    {
        return $this->em->getRepository('IndigoUserBundle:User')->findOneById($userId);
    }


    /**
     * @param $range
     * @return \Indigo\UserBundle\Entity\Game
     */
    public function doWeHaveWinner($range)
    {
        $topScore = 2;
        $tableId = 1;

        $tableStatus = $this->em->getRepository('IndigoGameBundle:TableStatus')->findOneById($tableId);

        $getLastGame = false;

        if($tableStatus && $tableStatus->getGameId()) {

            $qb2 = $this->em->getRepository('IndigoGameBundle:Game')->createQueryBuilder('p');
            $qb2->where("p.id = :gameId")
                ->andWhere('p.status=:started AND p.team0Score=:zero AND p.team1Score=:zero')
                ->setParameter('gameId', $tableStatus->getGameId())
                ->setParameter('started', 'started')
                ->setParameter('zero', 0);

            if ($qb2->getQuery()->getResult()) {
                //$date = new \DateTime(date("Y-m-d H:i:s"));
                //$date = $date->sub(new \DateInterval('PT' . $range . 'S'));

                $qb = $this->em->getRepository('IndigoGameBundle:Game')->createQueryBuilder('p');
                $qb->where("p.status = 'finished'")->addOrderBy('p.finishedAt', 'DESC');

                if ($qb->getQuery()->getResult()) {
                    return $qb->getQuery()->getResult()[0];
                }
            }
        }
        return null;
    }

    public function getLastWinnerModel($range)
    {
        $game = $this->doWeHaveWinner($range);

        if($game) {
            $model = new WinnerTeamModel();
            $model->setScore($game->getTeam0Score() . "-" . $game->getTeam1Score());

            if ($game->getTeam0Score() > $game->getTeam1Score()) {
                if ($game->getTeam0Player0Id()) {
                    $model->getPlayer1()->setImageUrl($game->getTeam0Player0Id()->getPicture());
                }
                if ($game->getTeam0Player1Id()) {
                    $model->getPlayer2()->setImageUrl($game->getTeam0Player1Id()->getPicture());
                }
            } else {

                if ($game->getTeam1Player0Id()) {
                    $model->getPlayer1()->setImageUrl($game->getTeam1Player0Id()->getPicture());
                }
                if ($game->getTeam1Player1Id()) {
                    $model->getPlayer2()->setImageUrl($game->getTeam1Player1Id()->getPicture());
                }
            }
            return $model;
        }
        return null;
    }


}