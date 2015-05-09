<?php

namespace Indigo\UIBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

class DashboardController extends Controller {


    /**
     * @Template()
     * @return JsonResponse
     */
    public function dashboardAction($id)
    {
        $model = $this->getDashboardViewService()->getDashboardViewModel($id);
        return $this->render('IndigoUIBundle:Dashboard:dashboard.html.twig', $model->jsonSerialize() );
    }

    private function getDashboardViewService()
    {
        return $this->get('indigo_ui.dashboard_view_service');
    }
}