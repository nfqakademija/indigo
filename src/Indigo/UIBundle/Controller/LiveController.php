<?php

namespace Indigo\UIBundle\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
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
    public function liveAction($id)
    {
        $model = $this->getIndigoStatsService()->getTableStatus(1);
        return $this->render('IndigoUIBundle:Live:live.html.twig', $model->jsonSerialize() );
    }

    /**
     * @param int
     * @return Response
     */
    public function statusAction($id)
    {
        $model = $this->getIndigoStatsService()->getTableStatus(1);
        return new Response(json_encode( $model ));
    }

    /**
     * @param int
     * @return array
     */
    public function widgetAction($id)
    {
        return $this->render('IndigoUIBundle:Live:widget.html.twig',[] );
        //$model = $this->getIndigoStatsService()->getTableStatus(1);
        //return new Response(json_encode( $model ));
    }

    /**
     * @return \Indigo\UIBundle\Services\LiveViewService
     */
    private function getIndigoStatsService()
    {
        return $this->get('indigo_ui.live_view_service');
    }


}
