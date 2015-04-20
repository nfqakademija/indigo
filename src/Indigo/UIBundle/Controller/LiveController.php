<?php

namespace Indigo\UIBundle\Controller;
use Indigo\UIBundle\Models\LiveViewModel;
use Indigo\UIBundle\Models\ContestModel;
use Indigo\UIBundle\Models\TeamModel;
use Indigo\UIBundle\Models\PlayerModel;

use Indigo\UIBundle\Services\IndigoService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class LiveController extends Controller
{
    /**
     * @Template()
     * @return Response
     */
    public function liveAction()
    {
        $is = new IndigoService();
        $model = $is->getTableStatus(0);
        return $model->jsonSerialize();
    }

    public function statusAction()
    {
        $is = new IndigoService();
        $model = $is->getTableStatus(0);
        $json = json_encode( $model->jsonSerialize() );
        return new JsonResponse($json);
    }
}
