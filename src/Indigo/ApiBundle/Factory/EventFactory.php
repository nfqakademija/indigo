<?php


namespace Indigo\ApiBundle\Factory;



use Indigo\ApiBundle\Model\AutoGoalEvent;
use Indigo\ApiBundle\Model\CardSwipeEvent;
use Indigo\ApiBundle\Model\TableShakeEvent;


class EventFactory
{
    public static function factory($data)
    {
        switch($data->type) {

            case 'CardSwipe':

                return new CardSwipeEvent($data);
            case 'TableShake':
                    
                return new TableShakeEvent($data);
            case 'AutoGoal':

                return new AutoGoalEvent($data);
            default:
                //TODO: alert - unknown table event
        }
    }
}
