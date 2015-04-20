<?php

namespace Indigo\UIBundle\Controller;

use Indigo\UIBundle\Models\ScoreModel;
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
        return [
            "player1" => [
                "name" => "Vardas1",
                "imageUrl" => "/bundles/indigoui/images/anonymous .png"
            ],

            "player2" => [
                "name" => "Vardas2",
                "imageUrl" => "/bundles/indigoui/images/anonymous .png"
            ],

            "player3" => [
                "name" => "Vardas3",
                "imageUrl" => "/bundles/indigoui/images/anonymous .png"
            ],

            "player4" => [
                "name" => "Vardas4",
                "imageUrl" => "/bundles/indigoui/images/anonymous .png"
            ],

            "score" => [
                "teamA" => "10",
                "teamB" => "2"
            ],


            "reservations" => [
                [
                    "isReserved" => true,
                    "name" => "Kestas",
                    "timeFrom" => "12:45",
                    "timeTo" => "13:00",
                    "imageUrl" => "/bundles/indigoui/images/anonymous .png"
                ],
                [
                    "isReserved" => false,
                    "name" => "Jurgis",
                    "timeFrom" => "13:00",
                    "timeTo" => "13:15",
                    "imageUrl" => "/bundles/indigoui/images/anonymous .png"
                ],
                [
                    "isReserved" => false,
                    "name" => "Jurgis",
                    "timeFrom" => "13:00",
                    "timeTo" => "13:15",
                    "imageUrl" => "/bundles/indigoui/images/anonymous .png"
                ],
                [
                    "isReserved" => true,
                    "name" => "Jurgis",
                    "timeFrom" => "13:00",
                    "timeTo" => "13:15",
                    "imageUrl" => "/bundles/indigoui/images/anonymous .png"
                ],
            ]

        ];
    }


    public function statusAction()
    {
        // Service required !!!

        $json = '{
            "status" : "Stalas laisvas",
            "score" : {"teamA" : 6, "teamB" : 7} ,
            "players" : [
                {"name" : "Dalia", "imageUrl" : "/bundles/indigoui/images/dalia.png"},
                {"name" : "Tadas", "imageUrl" : "/bundles/indigoui/images/tadas_surgailis.png"},
                {"name" : "Billas", "imageUrl" : "/bundles/indigoui/images/bilas.png"},
                {"name" : "Clintonas", "imageUrl" : "/bundles/indigoui/images/clinton.png"}
                ]
            }';

        return new JsonResponse($json);
    }
}
