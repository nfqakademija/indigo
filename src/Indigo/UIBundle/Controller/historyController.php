<?php
namespace Indigo\UIBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Response;
use Indigo\UIBundle\Models\GameHistoryViewModel;
use Indigo\UIBundle\Services\GameHistoryViewService;


class HistoryController extends Controller {

    public function __construct()
    {
    }

    /**
     * @Template()
     * @return Response
     */
    public function historyAction($contestId, $teamId)
    {
        $gs= $this->getGameHistoryViewService();
        $model = $gs->getViewModel($contestId, $teamId);
        return $this->render('IndigoUIBundle:History:history.html.twig', $model->jsonSerialize() );
    }


    private function getGameHistoryViewService()
    {
        return $this->get('indigo_ui.game_history.service');
    }

}