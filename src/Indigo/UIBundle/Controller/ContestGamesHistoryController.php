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
        $gamesQuery = $this
            ->getHistoryService()
            ->getGamesQuery($contestId);

        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $gamesQuery,
            $this->get('request')->query->get('page', 1),
            50
        );

        return $this->render('IndigoUIBundle:History:contest_games.html.twig',
            ['pagination' => $pagination]
        );
    }

    /**
     * @return \Indigo\UIBundle\Services\ContestGamesViewService
     */
    private function getHistoryService()
    {
        return $this->get('indigo_ui.contest_games_history.service');
    }
}