<?php
/**
 * Created by PhpStorm.
 * User: poweruser
 * Date: 4/4/2015
 * Time: 3:09 PM
 */

namespace Indigo\MainBundle\EventListener;

use Indigo\MainBundle\Event\ApiEvent;

class ApiSuccessListener
{
    public function onSuccess(ApiEvent $event)
    {
        var_dump($event);
    }
}