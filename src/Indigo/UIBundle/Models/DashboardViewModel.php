<?php
/**
 * Created by PhpStorm.
 * User: TSU
 * Date: 2015.04.25
 * Time: 14:16
 */

namespace Indigo\UIBundle\Models;
use JsonSerializable;

class DashboardViewModel implements \JsonSerializable {

    /**
     * @var ContestModel
     */
    private $currentContest;
    /**
     * @var ContestModel
     */
    private $nextContest;

    public function __construct()
    {
        $this->currentContest = new ContestModel();
        $this->nextContest = new ContestModel();
    }

    /**
     * @return array
     */
    public function jsonSerialize() {
        return [
            "currentContest" => $this->currentContest,
            "nextContest" => $this->nextContest
        ];
    }


    /**
     * @return ContestModel
     */
    public function getCurrentContest()
    {
        return $this->currentContest;
    }

    /**
     * @param ContestModel $currentContest
     */
    public function setCurrentContest($currentContest)
    {
        $this->currentContest = $currentContest;
    }

    /**
     * @return ContestModel
     */
    public function getNextContest()
    {
        return $this->nextContest;
    }

    /**
     * @param ContestModel $nextContest
     */
    public function setNextContest($nextContest)
    {
        $this->nextContest = $nextContest;
    }

}