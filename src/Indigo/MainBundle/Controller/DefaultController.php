<?php

namespace Indigo\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
//use Symfony\Bundle\AsseticBundle;

class DefaultController extends Controller
{
    public function indexAction($name=null)
    {
        return $this->render(sprintf('IndigoMainBundle:Pixel:%s.twig', $name), array("name" => $name));
    }
}
