<?php
/**
 * Created by PhpStorm.
 * User: poweruser
 * Date: 4/4/2015
 * Time: 2:50 PM
 */

namespace Indigo\MainBundle\API;


use Indigo\API\Model\CardSwipeEvent;
use Indigo\API\Model\TableShake;

class EventFactory
{
    public static function factory(array $data)
    {
        switch($data['type']) {
            case 'CardSwipe':

                $event = new CardSwipeEvent();

                $event->setId($data['id']);

                return $event;
            case 'TableShake':

                $event = new TableShake();

                $event->setId($data['id']);

                return $event;
        }
    }
}