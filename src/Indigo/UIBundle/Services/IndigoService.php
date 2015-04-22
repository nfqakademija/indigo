<?php
/**
 * Created by PhpStorm.
 * User: TSU
 * Date: 2015.04.20
 * Time: 19:49
 */



namespace Indigo\UIBundle\Services;


use Indigo\GameBundle\Entity\GameRepository;
use Indigo\UIBundle\Models\ContestModel;
use Indigo\UIBundle\Models\LiveViewModel;
use Indigo\UIBundle\Models\PlayerModel;
use Indigo\UIBundle\Models\TeamModel;
use Symfony\Component\Security\Acl\Exception\Exception;

class IndigoService
{

    function __construct()
    {

    }

    public function getTableStatus($tableId)
    {
        $model = new LiveViewModel();
        $model->setIsTableBusy(true);

        $contest = new ContestModel();
        $contest->setId(0);
        $contest->setTitle("Open lyga");
        $contest->setDescription("Tai laisva lyga, kuroje žaidžia kiekvienas");
        $contest->getStartDate('2015.04.01');
        $contest->getEndData('2015.04.30');
        $model->setContest($contest);

        $playerA1 = new PlayerModel();
        $playerA1->setName("Dalia");
        $playerA1->setImageUrl("/bundles/indigoui/images/dalia.png");

        $playerA2 = new PlayerModel();
        $playerA2->setName("Bilas");
        $playerA2->setImageUrl("/bundles/indigoui/images/bilas.png");

        $playerB1 = new PlayerModel();
        $playerB1->setName("Tadas");
        $playerB1->setImageUrl("/bundles/indigoui/images/tadas_surgailis.png");

        $playerB2 = new PlayerModel();
        $playerB2->setName("Klintonas");
        $playerB2->setImageUrl("/bundles/indigoui/images/clinton.png");
        
        $teamA = new TeamModel();
        $teamA->setPlayer1( $playerA1 );
        $teamA->setPlayer2( $playerA2 );
        $teamA->setGoals(5);

        $teamB = new TeamModel();
        $teamB->setPlayer1( $playerB1 );
        $teamB->setPlayer2( $playerB2 );
        $teamB->setGoals(3);

        $model->setTeamA( $teamA );
        $model->setTeamB( $teamB );

        return $model;
    }

    public function getCurrentContestInfo()
    {
        return new Exception("This method is not implemented");
    }
}