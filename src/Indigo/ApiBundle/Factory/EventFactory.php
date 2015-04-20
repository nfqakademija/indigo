<?php


namespace Indigo\ApiBundle\Factory;

use Indigo\ApiBundle\Model\AutoGoalModel;
use Indigo\ApiBundle\Model\CardSwipeModel;
use Indigo\ApiBundle\Model\TableShakeModel;

class EventFactory
{
    public static function factory($data)
    {
        switch($data->type) {

            case 'CardSwipe':

                return new CardSwipeModel($data);
            case 'TableShake':
                    
                return new TableShakeModel($data);
            case 'AutoGoal':

                return new AutoGoalModel($data);
            default:
                //TODO: alert - unknown table event
        }
    }
}
