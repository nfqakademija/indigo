<?php
/**
 * Created by PhpStorm.
 * User: TSU
 * Date: 2015.04.20
 * Time: 22:06
 */

namespace Indigo\UIBundle\Models;


class ContestModel {

    private $id;
    private $title;
    private $description;
    private $startDate;
    private $endData;

    public function __construct()
    {

    }

    public function jsonSerialize() {
        return [
            "id" => $this->id,
            "title" => $this->title,
            "description" => $this->description,
            "startDate" => $this->startDate,
            "endData" => $this->endData
        ];
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param mixed $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->Description;
    }

    /**
     * @param mixed $Description
     */
    public function setDescription($Description)
    {
        $this->Description = $Description;
    }

    /**
     * @return mixed
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * @param mixed $startDate
     */
    public function setStartDate($startDate)
    {
        $this->startDate = $startDate;
    }

    /**
     * @return mixed
     */
    public function getEndData()
    {
        return $this->endData;
    }

    /**
     * @param mixed $endData
     */
    public function setEndData($endData)
    {
        $this->endData = $endData;
    }

}