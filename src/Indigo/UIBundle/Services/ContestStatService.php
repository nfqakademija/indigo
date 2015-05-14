<?php

namespace Indigo\UIBundle\Services;


use Doctrine\ORM\EntityManagerInterface;
use Indigo\GameBundle\Entity\Game;
use Indigo\GameBundle\Entity\PlayerTeamRelation;
use Indigo\GameBundle\Repository\GameStatusRepository;
use Indigo\UIBundle\Models\ContestStatModel;
use Indigo\UIBundle\Models\TeamViewModel;
use Indigo\UserBundle\Entity\User;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;


class ContestStatService implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;

    }

    public function getStats($contestId)
    {
        $contestStatModel = new ContestStatModel();
        $teamsStats = new \ArrayIterator();

        $qb = $this->em->createQueryBuilder()
            ->select('COUNT(g.teamWon) as wins, IDENTITY(g.teamWon) as teamId')
            ->from('Indigo\GameBundle\Entity\Game', 'g')
            ->groupBy('g.teamWon')
            ->orderBy('wins', 'DESC')
            ->where('g.teamWon is not NULL AND g.contestId = :contest AND g.status = :status AND g.isStat = :isStat')
            ->setMaxResults(3)
            ->setParameters([
                'contest' => (int)$contestId,
                'status' => GameStatusRepository::STATUS_GAME_FINISHED,
                'isStat' => Game::GAME_WITH_STATS
                ]);

        $topTeams = $qb->getQuery()->getResult();
        $position = 0;
        $teamsId = [];
        foreach ($topTeams as $row) {

            $position++;
            $teamId = $row['teamId'];
            $teamsId[] = $teamId;

            $playerTeamRelation = $this->em->getRepository('IndigoGameBundle:PlayerTeamRelation')->findBy(['teamId' =>$teamId]);
            if (!empty($playerTeamRelation)) {

                foreach ($playerTeamRelation as $team) {

                    if (!$teamsStats->offsetExists($position)) {


                        $teamViewModel = new TeamViewModel();
                        $teamViewModel
                            ->addPicture($team->getPlayer()->getPicture())
                            ->setWins($row['wins'])
                            ->setTeamRating($team->getTeam()->getTeamRatings($contestId));
                        $teamsStats->offsetSet($position, $teamViewModel);
                    } else {

                         $teamsStats->offsetGet($position)->addPicture($team->getPlayer()->getPicture());
                    }
                }
            }
        }
        if ($position < 3) {
            $teamViewModel = new TeamViewModel();
            $teamViewModel
                ->addPicture(User::NO_FACE)
                ->addPicture(User::NO_FACE);

            for ($i = 3; $i > $position; $i--) {

                $teamsStats->offsetSet($i, $teamViewModel);
            }
        }


        $contestStatModel->setTopTeams($teamsStats);

        $qb = $this->em->createQuery('SELECT g
            FROM Indigo\GameBundle\Entity\Game g
            WHERE g.contestId = :contestId AND g.isStat = :isStat')
            ->setParameters([
                'contestId' => (int)$contestId,
                'isStat' => Game::GAME_WITH_STATS
            ]);

        //total games, total teams, total goals - per abi komandas, fastest game, slowest game
        $games = $qb->getResult();
        $uniqTeams = [];
        foreach ($games as $game) {
            /** @var Game $game */

            $contestStatModel
                ->setFastestGameDuration($this->getFasterGameDuration($contestStatModel->getFastestGameDuration(), $game))
                ->setSlowestGameDuration($this->getSlowerGameDuration($contestStatModel->getSlowestGameDuration(), $game))
                ->incTotalGoals($game->getTeam0Score())
                ->incTotalGoals($game->getTeam1Score())
                ->incTotalGames();

            foreach (array($game->getTeam0()->getId(), $game->getTeam1()->getId()) as $teamId) {

                if (!isset($uniqTeams[$teamId])) {

                    $uniqTeams[$teamId] = 1;
                    $contestStatModel->incUniqTeams();
                }
            }

        }
        if ($stats = $this->getGamesPerDay($contestId)) {

            $contestStatModel->setStatsGamesPerHour($stats);
        }

        return $contestStatModel;
    }


    /**
     * @param $ts
     * @param Game $game
     * @return int
     */
    private function getFasterGameDuration($ts, Game $game)
    {
        if (!$ts) {

            return $game->getDuration();
        }

        $gd = $game->getDuration();

        if (!$gd) {

            return $ts;
        }

        return ($gd < $ts ? $gd : $ts);
    }

    /**
     * @param $ts
     * @param Game $game
     * @return int
     */
    private function getSlowerGameDuration($ts, Game $game)
    {
        if (!$ts) {

            return $game->getDuration();
        }

        $gd = $game->getDuration();

        if (!$gd) {

            return $ts;
        }

        return ($gd > $ts ? $gd : $ts);
    }

    private function getGamesPerDay($contestId)
    {
        $result = new \ArrayIterator();
        $qb = $this->em->createQuery("
          SELECT count(g.id) games, HOUR(g.startedAt) game_hour, DATE_FORMAT(g.startedAt, '%d%H') groupHour
            FROM Indigo\GameBundle\Entity\Game g
            WHERE (g.contestId = :contestId AND g.startedAt >  :s)
            GROUP BY groupHour
            ORDER BY groupHour ASC")
            ->setParameters([
                'contestId' => (int)$contestId,
                's' => date('Y-m-d',time() - 3600 * 24).'%'
            ]);

        $stats = $qb->getResult();
        if ($stats !== null) {

            foreach ($stats as $rowStat) {

                $result->offsetSet($rowStat['game_hour'], $rowStat);
            }
        }
        return $result;

    }
}