<?php

namespace Indigo\UIBundle\Services;

use Indigo\ContestBundle\Entity\Contest;
use Indigo\GameBundle\Entity\Game;
use Indigo\GameBundle\Entity\PlayerTeamRelation;
use Indigo\GameBundle\Entity\Rating;
use Indigo\GameBundle\Repository\GameStatusRepository;
use Indigo\UIBundle\Models\GameHistoryViewModel;
use Indigo\UIBundle\Models\GameModel;
use Indigo\UIBundle\Models\PlayerStatModel;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;


class GameHistoryViewService implements LoggerAwareInterface
{

    use LoggerAwareTrait;
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

     /**
     * @param \Doctrine\ORM\EntityManager $em
     */
    function __construct(\Doctrine\ORM\EntityManager $em)
    {
        $this->em = $em;

    }


    public function getViewModel($contestId, $teamId)
    {
        if (!(int)$contestId || !(int)$teamId) {

            throw new NotFoundHttpException();
        }

        $contestEntity = $this->em->getRepository('IndigoContestBundle:Contest')->findOneById((int)$contestId);
        if ($contestEntity == null) {

            throw new NotFoundHttpException();
        }
        $teamEntity  = $this->em->getRepository('IndigoGameBundle:Team')->findOneById($teamId);
        if ($teamEntity == null) {

            throw new NotFoundHttpException();
        }

        $qb = $this->em->createQueryBuilder();
        $qb
            ->select('g')
            ->from('IndigoGameBundle:Game', 'g')
            ->where('(g.team0 = :team OR g.team1 = :team)')
            ->andWhere('g.contestId = :contestId')
            ->andWhere('g.status = :status')
            ->orderBy('g.startedAt', 'DESC')
            ->setParameters(
                [
                    'status' => GameStatusRepository::STATUS_GAME_FINISHED,
                    'team' => $teamEntity,
                    'contestId' => $contestId,
                ]
            );

        $teamGames = $qb->getQuery()->getResult();

        /** @var Contest $contestEntity */
        $model =  new GameHistoryViewModel();
        foreach ($teamGames as $game) {

            /** @var Game $game */
            $gameModel = new GameModel();
            $gameModel
                ->setGameDuration($game->getDuration())
                ->setStartedAt($game->getStartedAt())
                ->setGameDuration($game->getDuration())
                ->setTeam0Score($game->getTeam0Score())
                ->setTeam1Score($game->getTeam1Score())
                ->setTeam0Rating($game->getTeam0()->getTeamRatings($contestId))
                ->setTeam1Rating($game->getTeam1()->getTeamRatings($contestId));

            if ($game->getIsStat() && $game->getTeamWon() !== null) {

                if ($game->getRatings()) {

                    foreach ($game->getRatings() as $ratingDiffs) {

                        /** @var Rating $ratingDiffs */
                         if ($ratingDiffs->getTeam() == $game->getTeam0()) {

                             $gameModel->setTeam0RatingDiff($ratingDiffs->getRating());
                         } else {

                             $gameModel->setTeam1RatingDiff($ratingDiffs->getRating());
                         }
                    }
                }
            }
            foreach (array($game->getTeam0()->getPlayers(), $game->getTeam1()->getPlayers()) as $teamPosition => $teamPlayers) {

                if ($teamPlayers != null) {

                    foreach ($teamPlayers as $player) {

                        /** @var PlayerTeamRelation $player */
                        $gameModel->addPlayerPicture($teamPosition, $player->getPlayer()->getPicture());
                    }
                }
            }
            $model->addGame($gameModel);
        }

        return $model;
    }

};