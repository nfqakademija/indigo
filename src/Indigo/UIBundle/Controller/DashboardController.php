<?php
/**
 * Created by PhpStorm.
 * User: TSU
 * Date: 2015.04.23
 * Time: 00:36
 */

namespace Indigo\UIBundle\Controller;

use Indigo\UIBundle\Services\IndigoService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class DashboardController extends Controller {


    public function dashboardAction($name = "Petras")
    {
        return new Response("Hello");
    }
}