<?php

namespace Indigo\GameBundle\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Indigo\ContestBundle\Entity\Contest;
use Indigo\GameBundle\Entity\Game;
use Indigo\GameBundle\Repository\GameStatusRepository;
use Indigo\GameBundle\Repository\GameTypeRepository;

/**
 * Class GamePrePersistListener
 * @package Indigo\GameBundle\EventListener
 */
class GamePrePersistListener
{
    /**
     * @param Game $game
     * @param LifecycleEventArgs $event
     */
    public function prePersist(Game $game, LifecycleEventArgs $event)
    {
        $em = $event->getEntityManager();

        if ($game->getContest() == null) {

            $openContest = $em->getRepository('IndigoContestBundle:Contest')->findOneById(Contest::OPEN_CONTEST_ID);
            $game->setContest($openContest);
        }

        if ($game->getStartedAt() === null) {
            $game->setStartedAt(new \DateTime());
        }

        if ($game->getMatchType() === null) {

            $game->setMatchType(GameTypeRepository::TYPE_GAME_OPEN);
        }

        if ($game->getStatus() === null) {

            $game->setStatus(GameStatusRepository::STATUS_GAME_WAITING);
        }
    }
}
