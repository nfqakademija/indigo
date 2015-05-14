<?php

namespace Indigo\UIBundle\Services;

use Doctrine\ORM\EntityManagerInterface;
use Indigo\ContestBundle\Entity\Contest;
use Indigo\GameBundle\Entity\Game;
use Indigo\GameBundle\Repository\GameStatusRepository;
use Indigo\UIBundle\Models\GameHistoryViewModel;
use Indigo\UIBundle\Models\PlayerStatModel;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;


class GameHistoryViewService implements LoggerAwareInterface
{

    use LoggerAwareTrait;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var GameModelService
     */
    private $gameModelService;

    /**
     * @param EntityManagerInterface $em
     * @param GameModelService $gameModelService
     */
    function __construct(EntityManagerInterface $em, GameModelService $gameModelService)
    {
        $this->em = $em;
        $this->gameModelService = $gameModelService;
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
            ->andWhere('(g.team0Score > 0 OR g.team1Score > 0)')
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

            $gameModel = $this->gameModelService->getModel($contestId, $game);
            if ($gameModel !== null) {

                $model->addGame($gameModel);
            }

        }

        return $model;
    }

};