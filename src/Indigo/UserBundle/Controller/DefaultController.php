<?php

namespace Indigo\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class DefaultController extends Controller
{
    /**
     * @Route("/profile")
     * @Template()
     */
    public function indexAction($name="laima")
    {
        return array('name' => $name);
    }
}
