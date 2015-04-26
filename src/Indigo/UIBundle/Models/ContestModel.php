<?php
/**
 * Created by PhpStorm.
 * User: TSU
 * Date: 2015.04.20
 * Time: 22:06
 */

namespace Indigo\UIBundle\Models;


class ContestModel implements \JsonSerializable{

    /**
     * @var int
     */
    private $id;
    /**
     * @var string
     */
    private $title;
    /**
     * @var string
     */
    private $description;
    /**
     * @var datetime
     */
    private $startDate;
    /**
     * @var datetime
     */
    private $endData;

    /**
     * @var string
     */
    private $imageUrl;

    /**
     * @var datetime
     */
    private $dateFrom;

    /**
     * @var datetime
     */
    private  $dateTo;


    public function __construct()
    {
        $this->description = " ";
    }

    /**
     * @return array
     */
    public function jsonSerialize() {
        return [
            "id" => $this->id,
            "title" => $this->title,
            "description" => $this->description,
            "startDate" => $this->startDate,
            "endData" => $this->endData,
            "imageUrl" => $this->imageUrl
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

    /**
     * @return string
     */
    public function getImageUrl()
    {
        return $this->imageUrl;
    }

    /**
     * @param string $imageUrl
     */
    public function setImageUrl($imageUrl)
    {
        $this->imageUrl = $imageUrl;
    }

    /**
     * @return datetime
     */
    public function getDateFrom()
    {
        return $this->dateFrom;
    }

    /**
     * @param datetime $dateFrom
     */
    public function setDateFrom($dateFrom)
    {
        $this->dateFrom = $dateFrom;
    }

    /**
     * @return datetime
     */
    public function getDateTo()
    {
        return $this->dateTo;
    }

    /**
     * @param datetime $dateTo
     */
    public function setDateTo($dateTo)
    {
        $this->dateTo = $dateTo;
    }


}