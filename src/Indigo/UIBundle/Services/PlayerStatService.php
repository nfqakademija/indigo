<?php

namespace Indigo\UIBundle\Services;


use Indigo\GameBundle\Entity\PlayerTeamRelation;
use Indigo\GameBundle\Entity\Team;
use Indigo\GameBundle\Entity\TeamRepository;
use Indigo\GameBundle\Repository\GameStatusRepository;
use Indigo\UIBundle\Models\PlayerTeamStatModel;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NoResultException;
use Indigo\GameBundle\Entity\Game;
use Indigo\UserBundle\Entity\User;


use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class PlayerStatService implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    const STATE_WIN = 1;
    const STATE_LOSE = 0;
    const STATE_DRAW = -1;
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var User
     */
    private $userEntity;

    /**
     * @var Game
     */
    private $lastGame;

    /**
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em, TokenStorageInterface $userToken)
    {
        $this->em = $em;
        $this->userEntity = $userToken->getToken()->getUser();
    }

    public function getStats($contestId)
    {
        $teamsStats = new \ArrayIterator();

        $qb = $this->em->createQuery('SELECT g
            FROM Indigo\GameBundle\Entity\Game g
            WHERE
            (g.team0Player0Id = :player OR g.team0Player1Id = :player OR g.team1Player0Id = :player OR g.team1Player1Id = :player)
            AND g.contestId = :contest AND g.status = :status AND g.isStat = :isStat
            ')->setParameters([
                'player'=> $this->userEntity->getId(),
                'contest' => (int)$contestId,
                'status' => GameStatusRepository::STATUS_GAME_FINISHED,
                'isStat' => Game::GAME_WITH_STATS
                ]);

        $games = $qb->getResult();

        foreach ($games as $game) {

            /** @var Game $game */
            $teamEntity = $this->getTeam($game);
            $teamId = $teamEntity->getId();
            //$teamsStat

            if (!$teamsStats->offsetExists($teamId)) {

                $teamsStats->offsetSet($teamId,  $this->prepareStatModel($game, $teamEntity));
            }
            /** @var PlayerTeamStatModel $teamStat */
            $teamStat = $teamsStats->offsetGet($teamId);
            switch ($this->isGameWon($game)) {

                case self::STATE_WIN:
                    $teamStat->setFastestWinGameTs($this->getFasterGameDuration($teamStat->getFastestWinGameTs(), $game));
                    $teamStat->setSlowestWinGameTs($this->getSlowerGameDuration($teamStat->getSlowestWinGameTs(), $game));
                    $teamStat->addWins();
                    break;

                case self::STATE_LOSE:
                    $teamStat->addLosses();
                    break;
            }

            $teamStat->addScoredGoals($this->getScoredGoals($game));
            $teamStat->addMissedGoals($this->getMissedGoals($game));
        }

        if ($teamsStats->count()) {

            $teamsStats->uasort(array($this,'sortByWins'));
        } else {

            $qb = $this->em->createQuery('SELECT t
            FROM Indigo\GameBundle\Entity\Team t
            LEFT JOIN Indigo\GameBundle\Entity\PlayerTeamRelation pt
            WITH (t.id = pt.teamId)
            WHERE t.isSingle = :isSingle AND pt.player = :player
            ')->setParameters([
                    'player'=> $this->userEntity->getId(),
                    'isSingle' => 1
                ]);

            try {

                /** @var Team $singleTeam */
                $singleTeam = $qb->getSingleResult();
                $teamsStats->offsetSet($singleTeam->getId(), $this->prepareSinglePlayerStatModel($contestId, $singleTeam));
            } catch (NoResultException $e) {

                $this->logger && $this->logger->error('player has no single team!'. $e->getMessage());
            }
        }
        return $teamsStats;
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

    /**
     * @param $a
     * @param $b
     * @return int
     */
    public function sortByWins($a, $b)
    {
        if ($a->getWins() == $b->getWins()) {

            if ($a->getLosses() > $b->getLosses()) {

                return 1;
            } else {

                return -1;
            }

            return 0;
        } elseif ($a->getWins() > $b->getWins()) {

            return -1;
        }
        return 1;
    }

    /**
     * @param Game $game
     * @return int
     */
    private function getPlayerTeamPosition(Game $game)
    {
        if ($game->getTeam0Player0Id() == $this->userEntity ||
            $game->getTeam0Player1Id() == $this->userEntity) {

            return 0;
        }

        return 1;
    }



    /**
     * @param Game $game
     * @return \Indigo\GameBundle\Entity\Team
     */
    private function getTeam(Game $game)
    {
         return $game->getTeam($this->getPlayerTeamPosition($game));
    }

    /**
     * @param Game $game
     * @return int
     */
    private function isGameWon(Game $game)
    {
        return ($game->getWinnerTeamPosition() == $this->getPlayerTeamPosition($game) ? self::STATE_WIN : self::STATE_LOSE);
    }

    /**
     * @param Game $game
     * @return int
     */
    private function getScoredGoals(Game $game)
    {
        return (int)$game->getTeamScore($this->getPlayerTeamPosition($game));
    }

    /**
     * @param Game $game
     * @return int
     */
    private function getMissedGoals(Game $game)
    {
        return (int)$game->getTeamScore( (1 - $this->getPlayerTeamPosition($game)));
    }

    public function prepareStatModel(Game $game, Team $teamEntity)
    {
        $model =  new PlayerTeamStatModel();
        $model->setTeamRating($teamEntity->getTeamRatings($game->getContestId()));
        $teamPlayerRelations = $teamEntity->getPlayers();
        foreach ($teamPlayerRelations as $teamPlayerRelation) {

            /** @var PlayerTeamRelation $player */
            $model->addPicture($teamPlayerRelation->getPlayer()->getPicture());
        }
        return $model;
    }

    public function prepareSinglePlayerStatModel($contestId, Team $teamEntity)
    {
        $model =  new PlayerTeamStatModel();
        $model->setTeamRating($teamEntity->getTeamRatings($contestId));
        $teamPlayerRelations = $teamEntity->getPlayers();
        foreach ($teamPlayerRelations as $teamPlayerRelation) {

            /** @var PlayerTeamRelation $player */
            $model->addPicture($teamPlayerRelation->getPlayer()->getPicture());
        }
        return $model;
    }

}