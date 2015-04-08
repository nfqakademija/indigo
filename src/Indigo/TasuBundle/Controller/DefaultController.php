<?php

namespace Indigo\TasuBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class DefaultController extends Controller
{
    /**
     * @Route("/default/")
     * @Template()
     */
    public function indexAction()
    {
        global $number;
        $number++;
        // renders app/Resources/views/hello/index.html.twig
        return $this->render('IndigoTasuBundle:Default:index.html.twig', $number);
    }
}
