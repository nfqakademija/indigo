<?php
/**
 * Created by PhpStorm.
 * User: poweruser
 * Date: 4/4/2015
 * Time: 3:05 PM
 */

namespace Indigo\MainBundle\Event;

use Symfony\Component\EventDispatcher\Event;

class ApiEvent extends Event
{
    private $data;

    public function __construct()
    {

    }

    public function setData($data)
    {
        $this->data = $data;
    }
}