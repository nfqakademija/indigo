<?php

namespace Indigo\UIBundle\Services;

use Indigo\ContestBundle\Entity\Contest;
use Indigo\GameBundle\Entity\GameTime;
use Indigo\UIBundle\Models\DashboardViewModel;
use Indigo\UIBundle\Models\PlayerStatModel;
use Indigo\UIBundle\Models\ReservationModel;
use Indigo\UserBundle\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class DashboardViewService
{

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * @var PlayerStatService
     */
    private $playerStatService;

    /**
     * @var ContestStatService
     */
    private $contestStatService;

    /**
     * @var User
     */
    private $userEntity;

    /**
     * @param \Doctrine\ORM\EntityManager $em
     */
    function __construct(\Doctrine\ORM\EntityManager $em, TokenStorageInterface $userToken, PlayerStatService $playerStatService, ContestStatService $contestStatService)
    {
        $this->em = $em;
        $this->userEntity = $userToken->getToken()->getUser();
        $this->playerStatService = $playerStatService;
        $this->contestStatService = $contestStatService;
    }

    public function getDashboardViewModel($contestId)
    {
        $contestEntity = $this->em->getRepository('IndigoContestBundle:Contest')->findOneById((int)$contestId);
        if ($contestEntity == null) {
            $contestId = Contest::OPEN_CONTEST_ID;
            $contestEntity = $this->em->getRepository('IndigoContestBundle:Contest')->findOneById((int)$contestId);
        }
        $nextContest = $this->em->getRepository('IndigoContestBundle:Contest')->getNextContest();
        /** @var Contest $contestEntity */

        $tableId = 1;
        $tableStatus = $this->em->getRepository('IndigoGameBundle:TableStatus')->findOneById($tableId);

        $model = new DashboardViewModel();
        $model->setTeamAScore(0);
        $model->setTeamBScore(0);
        $model->setIsTableBusy(false);

        if ($tableStatus) {
            $model->setIsTableBusy($tableStatus->getBusy());

            if ($tableStatus->getGame()) {
                $game = $this->em->getRepository('IndigoGameBundle:Game')->find($tableStatus->getGame());
                $model->setTeamAScore($game->getTeam0Score());
                $model->setTeamBScore($game->getTeam1Score());
            }
        }

        $model->getCurrentContest()->setId($contestEntity->getId());
        $model->getCurrentContest()->setTitle($contestEntity->getContestTitle());
        $model->getCurrentContest()->setDescription($contestEntity->getPrize());
        $model->getCurrentContest()->setImageUrl($contestEntity->getPathForImage());
        $model->getCurrentContest()->setDateFrom($contestEntity->getContestStartingDate()->format("Y-m-d"));
        $model->getCurrentContest()->setDateTo($contestEntity->getContestEndDate()->format("Y-m-d"));

        if ($nextContest) {
            $model->getNextContest()->setTitle($nextContest->getContestTitle());
            $model->getNextContest()->setDescription($nextContest->getPrize());
            $model->getNextContest()->setImageUrl($nextContest->getPathForImage());
            $model->getNextContest()->setDateFrom($nextContest->getContestStartingDate()->format("Y-m-d"));
            $model->getNextContest()->setDateTo($nextContest->getContestEndDate()->format("Y-m-d"));
        }


        if ($reservation = $this->em->getRepository('IndigoGameBundle:GameTime')->getMyNextReservation($this->userEntity)) {
            $model->setNextReservation(new ReservationModel());
            /** @var GameTime $reservation */
            $model->getNextReservation()->setDateStart($reservation->getStartAt()->format("Y-m-d H:i"));
        }

        $model->setPlayerTeamsStats($this->playerStatService->getStats($contestId));
        $model->setContestStat($this->contestStatService->getStats($contestId));

        return $model;
    }

};