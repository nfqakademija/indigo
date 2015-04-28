<?php
/**
 * Created by PhpStorm.
 * User: TSU
 * Date: 2015.04.26
 * Time: 23:55
 */

namespace Indigo\UIBundle\Services;


use Indigo\UIBundle\Models\DashboardViewModel;
use Indigo\UIBundle\Models\ReservationModel;

class DashboardViewService {

    private $em;

    /**
     * @param \Doctrine\ORM\EntityManager $em
     */
    function __construct(\Doctrine\ORM\EntityManager $em)
    {
        $this->em = $em;
    }


    public function getDashboardViewModel()
    {
        $tableId = 1;
        $tableStatus = $this->em->getRepository('IndigoGameBundle:TableStatus')->findOneById($tableId);

        $model = new DashboardViewModel();
        $model->setTeamAScore(0);
        $model->setTeamBScore(0);
        $model->setIsTableBusy(false);

        if($tableStatus)
        {
            $model->setIsTableBusy($tableStatus->getBusy());

            if($tableStatus->getGame())
            {
                $game = $this->em->getRepository('IndigoGameBundle:Game')->find($tableStatus->getGame());
                $model->setTeamAScore($game->getTeam0Score());
                $model->setTeamBScore($game->getTeam1Score());
            }

        }





        $model->getCurrentContest()->setTitle("Super turnyras");
        $model->getCurrentContest()->setDescription("Kelionė dviems į saulėtąją Turkiją.");
        $model->getCurrentContest()->setImageUrl('/bundles/indigoui/images/content-box.png');
        $model->getCurrentContest()->setDateFrom("2015-01-02");
        $model->getCurrentContest()->setDateTo("2015-02-02");

        $model->getNextContest()->setTitle("Greičio turnyras");
        $model->getNextContest()->setDescription("Adrenalino pilnas pasivažinėjimas kartingais.");
        $model->getNextContest()->setImageUrl('/bundles/indigoui/images/content-box-2.png');
        $model->getNextContest()->setDateFrom("2015-02-02");
        $model->getNextContest()->setDateTo("2015-03-02");



        $model->setNextReservation(new ReservationModel());
        $model->getNextReservation()->setDateStart("2012-02-01 15:00");


        return $model;
    }
}