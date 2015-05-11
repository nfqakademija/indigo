<?php

namespace Indigo\UIBundle\Services;

use Indigo\GameBundle\Entity\Game;
use Indigo\GameBundle\Entity\PlayerTeamRelation;
use Indigo\UIBundle\Models\GameModel;

class GameModelService
{
    /**
     * @param integer $contestId
     * @param Game $game
     * @return GameModel
     */
    public function getModel($contestId, Game $game)
    {
        $team0Players = [];
        $team1Players = [];

        $gameModel = new GameModel();
        $gameModel
            ->setIsStat($game->getIsStat())
            ->setGameDuration($game->getDuration())
            ->setStartedAt($game->getStartedAt())
            ->setGameDuration($game->getDuration())
            ->setTeam0Score($game->getTeam0Score())
            ->setTeam1Score($game->getTeam1Score());
        if ($game->getTeam0()) {

            $gameModel->setTeam0Rating($game->getTeam0()->getTeamRatings($contestId));
            $team0Players = $game->getTeam0()->getPlayers();
        }
        if ($game->getTeam1()) {

            $team1Players = $game->getTeam1()->getPlayers();
            $gameModel->setTeam1Rating($game->getTeam1()->getTeamRatings($contestId));
        }

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



        foreach (array($team0Players, $team1Players) as $teamPosition => $teamPlayers) {

            if ($teamPlayers != null) {

                foreach ($teamPlayers as $player) {

                    /** @var PlayerTeamRelation $player */
                    $gameModel->addPlayerPicture($teamPosition, $player->getPlayer()->getPicture());
                }
            }
        }

        return $gameModel;
    }
}