<?php

namespace Indigo\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        if (!$name) {
            $name='index.html';
        }
        return $this->render(sprintf('IndigoMainBundle:Pixel:%s.twig', $name), array("name" => $name));
    }
}
