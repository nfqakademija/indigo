<?php

namespace Indigo\UIBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;


class PlayerGamesHistoryController extends Controller
{

    /**
     * @Template()
     * @return Response
     */
    public function historyAction($contestId, $teamId)
    {
        $gs= $this->getGameHistoryViewService();
        $model = $gs->getViewModel($contestId, $teamId);

        return $this->render('IndigoUIBundle:History:player_games.html.twig', $model->jsonSerialize() );
    }

    private function getGameHistoryViewService()
    {
        return $this->get('indigo_ui.game_history.service');
    }

}