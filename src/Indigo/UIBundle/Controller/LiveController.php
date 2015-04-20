<?php

namespace Indigo\UIBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
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
        // Service required
        return new Response("Stalas užimtas");
    }

    public function scoreAction()
    {
        // Service required
        $score = '{ "teamA" : 6, "teamB" : 9 }';
        return new Response($score);
    }

    public function player1Action()
    {
        // Service required
        $info = '{ "name" : "Jonas", "imageUrl" : "/bundles/indigoui/images/tadas_surgailis.png" }';
        return new Response($info);
    }

    public function player2Action()
    {
        // Service required
        $info = '{ "name" : "Petras", "imageUrl" : "/bundles/indigoui/images/tadas_surgailis.png" }';
        return new Response($info);
    }

    public function player3Action()
    {
        // Service required
        $info = '{ "name" : "Jonas", "imageUrl" : "/bundles/indigoui/images/tadas_surgailis.png" }';
        return new Response($info);
    }

    public function player4Action()
    {
        // Service required
        $info = '{ "name" : "Jonas", "imageUrl" : "/bundles/indigoui/images/tadas_surgailis.png" }';
        return new Response($info);
    }




}
