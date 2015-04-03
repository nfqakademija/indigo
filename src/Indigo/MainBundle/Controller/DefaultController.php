<?php

namespace Indigo\MainBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="user_dashboard")
     */
    public function dashboardAction()
    {
        return $this->render('IndigoMainBundle:Pixel:index.html.twig', []);
    }

/*    public function indexAction($name)
    {
        if (!$name) {
            $name='index.html';
        }
        return $this->render(sprintf('IndigoMainBundle:Pixel:%s.twig', $name), array("name" => $name));
    }*/
}
