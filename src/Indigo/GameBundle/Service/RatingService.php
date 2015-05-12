<?php

namespace Indigo\GameBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use Indigo\GameBundle\Entity\Game;
use Indigo\GameBundle\Entity\Rating;
use Indigo\GameBundle\Repository\GameTypeRepository;

class RatingService
{

    /**
     * @var int The K Factor used.
     */
    const KFACTOR = 16;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @param EntityManagerInterface $em
     */
    public function __construct (EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    private function getTeam0WinRatio($score0, $score1)
    {
        if ($score0 > $score1) {

            return 1;
        } elseif ($score0 < $score1) {

            return 0;
        }

        return 0.5;
    }

    /**
     * @param Game $gameEntity
     * @return bool
     */
    public function updateRatings(Game $gameEntity)
    {

        if (! $gameEntity->getIsStat()) {

            return;
        }

        $team0WinRatio = $this->getTeam0WinRatio($gameEntity->getTeam0Score(), $gameEntity->getTeam1Score());

        $matchType = $gameEntity->getMatchType(); // open/match

        if ($matchType == GameTypeRepository::TYPE_GAME_OPEN) {

            $ratings = $this->newRatings(
                $gameEntity->getTeam0()->getOpenRating(),
                $gameEntity->getTeam1()->getOpenRating(),
                $team0WinRatio
            );
        } else {

            $ratings = $this->newRatings(
                $gameEntity->getTeam0()->getContestRating(),
                $gameEntity->getTeam1()->getContestRating(),
                $team0WinRatio
            );
        }

        $this->save($gameEntity, $ratings);
        return true;
    }

    private function newRatings($team0Rating, $team1Rating, $team0WinRatio)
    {

        $expectedScoreTeam0 = 1 / ( 1 + ( pow( 10 , ($team1Rating - $team0Rating  ) / 400 ) ) );
        $ratingDiffTeam0 = round( self::KFACTOR * ( $team0WinRatio - $expectedScoreTeam0 ));

        $newRatingTeam0 = $team0Rating + $ratingDiffTeam0 ;
        $newRatingTeam1 = $team1Rating + $ratingDiffTeam0 * (-1);



        return [
            0 => [
                'rating' => $newRatingTeam0,
                'diff' => $ratingDiffTeam0
            ],
            1 => [
                'rating' => $newRatingTeam1,
                'diff' => $ratingDiffTeam0 * (-1)
            ]
        ];
    }

    /**
     * @param Game $gameEntity
     * @param Array $ratings
     */
    private function save(Game $gameEntity, $ratings)
    {
        $this->saveGameDiffRatings(0, $gameEntity, $ratings); // team0
        $this->saveGameDiffRatings(1, $gameEntity, $ratings); // team1

        $this->em->flush();
    }

    /**
     * @param integer $teamPosition
     * @param Game $gameEntity
     * @param $ratings
     * @return bool
     */
    private function saveGameDiffRatings($teamPosition, Game $gameEntity, $ratings)
    {
        $ratingEntity = new Rating();
        $teamEntity = $gameEntity->getTeam($teamPosition);
        $ratingEntity->setGame($gameEntity);
        if ($gameEntity->getMatchType() == GameTypeRepository::TYPE_GAME_OPEN) {

            $teamEntity->setOpenRating($ratings[$teamPosition]['rating']);
        } else {

            $teamEntity->setContestRating($ratings[$teamPosition]['rating']);
        }
        $ratingEntity->setTeam($teamEntity);
        $ratingEntity->setRating($ratings[$teamPosition]['diff']);

        $this->em->persist($ratingEntity);

        return true;
    }
}