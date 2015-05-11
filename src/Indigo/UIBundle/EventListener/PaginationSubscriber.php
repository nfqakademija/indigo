<?php

namespace Indigo\UIBundle\EventListener;

use Indigo\GameBundle\Entity\Game;
use Indigo\UIBundle\Models\ContestGamesViewModel;
use Indigo\UIBundle\Services\GameModelService;
use Knp\Component\Pager\Event\AfterEvent;
use Symfony\Component\Finder\Finder;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Knp\Component\Pager\Event\ItemsEvent;

class PaginationSubscriber implements EventSubscriberInterface
{
    /**
     * @var GameModelService
     */
    private $gameModelService;

    public function __construct($gameModelService)
    {
        $this->gameModelService = $gameModelService;
    }

    public function items(AfterEvent $event)
    {
        $games = [];
        /** @var Game $game */
        foreach ($event->getPaginationView()->getItems() as $game) {

            $gameModel = $this->gameModelService->getModel($game->getContestId(), $game);
            if ($gameModel !== null) {
                $games[] = $gameModel;
            }
        }


        $event->getPaginationView()->setItems($games);
    }

    public static function getSubscribedEvents()
    {
        return array(
            'knp_pager.after' => array('items', 1/*increased priority to override any internal*/)
        );
    }
}
