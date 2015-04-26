<?php
/**
 * Created by PhpStorm.
 * User: TSU
 * Date: 2015.04.26
 * Time: 23:55
 */

namespace Indigo\UIBundle\Services;


use Indigo\UIBundle\Models\DashboardViewModel;

class DashboardViewService {

    public function getDashboardViewModel()
    {
        $model = new DashboardViewModel();
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


        return $model;
    }
}