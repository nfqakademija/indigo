<?php

namespace Indigo\UIBundle\Services;

use Indigo\ContestBundle\Entity\Contest;
use Indigo\GameBundle\Entity\Game;
use Indigo\GameBundle\Repository\GameStatusRepository;
use Indigo\UIBundle\Models\ContestGamesViewModel;
use Indigo\UIBundle\Models\PlayerStatModel;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;


class ContestGamesViewService implements LoggerAwareInterface
{

    use LoggerAwareTrait;
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * @var GameModelService
     */
    private $gameModelService;

     /**
     * @param \Doctrine\ORM\EntityManager $em
     */
    function __construct(\Doctrine\ORM\EntityManager $em, GameModelService $gameModelService)
    {
        $this->em = $em;
        $this->gameModelService = $gameModelService;

    }

    /**
     * @param $contestId
     * @return ContestGamesViewModel
     */
    public function getGamesQuery($contestId)
    {
        if (!(int)$contestId) {

            throw new NotFoundHttpException();
        }


        $contestEntity = $this->em->getRepository('IndigoContestBundle:Contest')->findOneById((int)$contestId);
        if ($contestEntity == null) {

            throw new NotFoundHttpException();
        }

        $qb = $this->em->createQueryBuilder();
        $qb ->select('g, r, t0, t1')
            ->from('IndigoGameBundle:Game', 'g')
            ->leftJoin('g.ratings', 'r')
            ->leftJoin('g.team0', 't0')
            ->leftJoin('g.team1', 't1')
            ->where('g.contest = :contestId')
            ->andWhere('g.status = :gameStatus')
            ->orderBy('g.startedAt', 'DESC')
            ->setParameters(
                [
                    'contestId' => $contestId,
                    'gameStatus' => GameStatusRepository::STATUS_GAME_FINISHED
                ]);


        return $qb->getQuery();
    }
}
