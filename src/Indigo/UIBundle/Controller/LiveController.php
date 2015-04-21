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

    public function __construct()
    {

    }


    /**
     * @Template()
     * @return Response
     */
    public function liveAction()
    {
        $em = $this->get('doctrine.orm.entity_manager');

        $is = new IndigoService($em);
        $model = $is->getTableStatus(1);
        $json = json_encode( $model );
        return $model->jsonSerialize($json);
    }

    public function statusAction()
    {
        $em = $this->get('doctrine.orm.entity_manager');

        $is = new IndigoService($em);
        $model = $is->getTableStatus(1);
        $json = json_encode( $model );
        return new JsonResponse($json);
    }
}
