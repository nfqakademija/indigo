<?php

namespace Indigo\UIBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

class ContestGamesHistoryController extends Controller
{


    /**
     * @Template()
     * @return JsonResponse
     */
    public function historyAction($contestId)
    {
        $model = $this
                    ->getHistoryService()
                    ->getViewModel($contestId);
        return $this->render('IndigoUIBundle:History:contest_games.html.twig', $model->jsonSerialize());
    }

    /**
     * @return object
     */
    private function getHistoryService()
    {
        return $this->get('indigo_ui.contest_games_history.service');
    }
}