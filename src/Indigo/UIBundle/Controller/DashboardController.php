<?php
/**
 * Created by PhpStorm.
 * User: TSU
 * Date: 2015.04.23
 * Time: 00:36
 */

namespace Indigo\UIBundle\Controller;

use Indigo\UIBundle\Models\ContestModel;
use Indigo\UIBundle\Services\IndigoService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class DashboardController extends Controller {

    /**
     * @Template()
     * @return JsonResponse
     */
    public function dashboardAction()
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $is = new IndigoService($em);
        $model = $is->getDashboardViewModel();
        return $this->render('IndigoUIBundle:Dashboard:dashboard.html.twig', $model->jsonSerialize() );
    }
}