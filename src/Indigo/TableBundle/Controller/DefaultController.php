<?php

namespace Indigo\TableBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('IndigoTableBundle:Default:index.html.twig', array('name' => $name));
    }
}
