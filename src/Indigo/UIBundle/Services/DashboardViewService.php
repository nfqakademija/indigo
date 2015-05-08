<?php

namespace Indigo\UIBundle\Services;

use Indigo\ContestBundle\Entity\Contest;
use Indigo\UIBundle\Models\DashboardViewModel;
use Indigo\UIBundle\Models\PlayerStatModel;
use Indigo\UIBundle\Models\ReservationModel;

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
     * @param \Doctrine\ORM\EntityManager $em
     */
    function __construct(\Doctrine\ORM\EntityManager $em, PlayerStatService $playerStatService, ContestStatService $contestStatService)
    {
        $this->em = $em;
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


        $model->getCurrentContest()->setTitle($contestEntity->getContestTitle());
        $model->getCurrentContest()->setDescription("Kelionė dviems į saulėtąją Turkiją.");
        $model->getCurrentContest()->setImageUrl($contestEntity->getPathForImage());
        $model->getCurrentContest()->setDateFrom($contestEntity->getContestStartingDate()->format("Y-m-d"));
        $model->getCurrentContest()->setDateTo($contestEntity->getContestEndDate()->format("Y-m-d"));

        $model->getNextContest()->setTitle("Greičio turnyras");
        $model->getNextContest()->setDescription("Adrenalino pilnas pasivažinėjimas kartingais.");
        $model->getNextContest()->setImageUrl('/bundles/indigoui/images/content-box-2.png');
        $model->getNextContest()->setDateFrom("2015-02-02");
        $model->getNextContest()->setDateTo("2015-03-02");


        $model->setNextReservation(new ReservationModel());
        $model->getNextReservation()->setDateStart("2012-02-01 15:00");

        $model->setPlayerTeamsStats($this->playerStatService->getStats($contestId));
        $model->setContestStat($this->contestStatService->getStats($contestId));

        return $model;
    }

};