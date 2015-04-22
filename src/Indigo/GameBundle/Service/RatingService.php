<?php

namespace Indigo\GameBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use Indigo\GameBundle\Entity\Game;
use Indigo\GameBundle\Entity\Rating;

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
     * @var Game
     */
    private $gameEntity;

    /**
     * @var int
     */
    private $ratingTeam0;

    /**
     * @var int
     */
    private $ratingTeam1;

    /**
     * @var int
     */
    private $scoreTeam0;

    /**
     * @var int
     */
    private $scoreTeam1;

    /**
     * @param EntityManagerInterface $em
     */
    public function __construct (EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function setGame(Game $gameEntity)
    {
        $this->gameEntity = $gameEntity;

        $this
            ->setRatingTeam1(
                $gameEntity
                    ->getTeam1()
                    ->getTotalRating()
            )
            ->setRatingTeam0(
                $gameEntity
                    ->getTeam0()
                    ->getTotalRating()
            )
            ->setScoreTeam0(0)
            ->setScoreTeam1(0);

        $diff = ($this->gameEntity->getTeam0Score()  -  $this->gameEntity->getTeam1Score());
        if ($diff > 0) {

            $this->setScoreTeam0(1);
        } elseif ($diff < 0) {

            $this->setScoreTeam1(1);
        } else {

            $this
                ->setScoreTeam0(0.5)
                ->setScoreTeam1(0.5);
        }

    }

    /**
     * @return int
     */
    public function getRatingTeam0()
    {
        return $this->ratingTeam0;
    }

    /**
     * @param $ratingTeam0
     * @return $this
     */
    public function setRatingTeam0($ratingTeam0)
    {
        $this->ratingTeam0 = (int)$ratingTeam0;
        return $this;
    }

    /**
     * @return int
     */
    public function getRatingTeam1()
    {
        return $this->ratingTeam1;
    }

    /**
     * @param $ratingTeam1
     * @return $this
     */
    public function setRatingTeam1($ratingTeam1)
    {
        $this->ratingTeam1 = (int)$ratingTeam1;
        return $this;
    }

    /**
     * @return int
     */
    public function getScoreTeam0()
    {
        return $this->scoreTeam0;
    }

    /**
     * @param $scoreTeam0
     * @return $this
     */
    public function setScoreTeam0($scoreTeam0)
    {
        $this->scoreTeam0 = (int)$scoreTeam0;
        return $this;
    }

    /**
     * @return int
     */
    public function getScoreTeam1()
    {
        return $this->scoreTeam1;
    }

    /**
     * @param $scoreTeam1
     * @return $this
     */
    public function setScoreTeam1($scoreTeam1)
    {
        $this->scoreTeam1 = (int)$scoreTeam1;
        return $this;
    }




    public function getNewRatings()
    {
/*        $expectedScoreTeam0 = 1 / ( 1 + ( pow( 10 , ((int)$ratingTeam1 - (int)$ratingTeam0 ) / 400 ) ) );
        $expectedScoreTeam1 = 1 / ( 1 + ( pow( 10 , ((int)$ratingTeam0 - (int)$ratingTeam1 ) / 400 ) ) );
        $newRatingTeam0 = $ratingTeam0 + ( self::KFACTOR * ( $scoreTeam0 - $expectedScoreTeam0 ) );
        $newRatingTeam1 = $ratingTeam1 + ( self::KFACTOR * ( $scoreTeam1 - $expectedScoreTeam1 ) );*/

        $expectedScoreTeam0 = 1 / ( 1 + ( pow( 10 , ($this->getRatingTeam1() - $this->getRatingTeam0()  ) / 400 ) ) );
       // $expectedScoreTeam1 =  1 - $expectedScoreTeam0
        $ratingDiffTeam0 = round( self::KFACTOR * ( $this->getScoreTeam0() - $expectedScoreTeam0 ));

        $newRatingTeam0 = $this->getRatingTeam0() + $ratingDiffTeam0 ;
        $newRatingTeam1 = $this->getRatingTeam1() + $ratingDiffTeam0 * (-1);

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

    public function save()
    {
        $ratings = $this->getNewRatings();
        $ratingEntity = new Rating();
        $teamEntity = $this->gameEntity->getTeam0();
        $ratingEntity->setGame($this->gameEntity);
        $teamEntity->setTotalRating($ratings[0]['rating']);
        $ratingEntity->setTeam($teamEntity);
        $ratingEntity->setRating($ratings[0]['diff']);

        $this->em->persist($ratingEntity);

        $ratingEntity = new Rating();
        $teamEntity = $this->gameEntity->getTeam1();
        $ratingEntity->setGame($this->gameEntity);
        $teamEntity->setTotalRating($ratings[1]['rating']);
        $ratingEntity->setTeam($teamEntity);
        $ratingEntity->setRating($ratings[1]['diff']);

        $this->em->persist($ratingEntity);
        $this->em->flush();
    }

}