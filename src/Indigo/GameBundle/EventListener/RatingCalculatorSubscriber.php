<?php

namespace Indigo\GameBundle\EventListener;


use Indigo\GameBundle\Event\GameEvents;
use Proxies\__CG__\Indigo\GameBundle\Entity\Game;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Indigo\GameBundle\Event\GameFinishEvent;

class RatingCalculatorSubscriber extends Container implements EventSubscriberInterface
{
    /**
     * @var \Indigo\GameBundle\Service\RatingService
     */
    private $ratingService;

/*    public function __construct($ratingService)
    {
        $this->ratingService = $service;
    }*/

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            GameEvents::GAME_FINISH_ON_DOUBLE_SWIPE => ['calculateRatings', 1],
            GameEvents::GAME_FINISH_ON_SCORE => ['calculateRatings', 1]
        ];
    }

    /**
     * @param GameFinishEvent $event
     */
    public function calculateRatings(GameFinishEvent $event)
    {
        $gameEntity = $event->getGame();
        /** @var \Indigo\GameBundle\Service\RatingService */
        $this->ratingService->updateRatings($gameEntity);


        //if ($gameEntity->getPlayersCountInTeam(0) == $gameEntity->getPlayersCountInTeam(1)) {}

    }

    public function setRatingService ($service)
    {
        $this->ratingService = $service;
    }


}