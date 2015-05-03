<?php
/**
 * Created by PhpStorm.
 * User: TSU
 * Date: 2015.04.20
 * Time: 21:58
 */

namespace Indigo\UIBundle\Models;


class PlayerModel implements \JsonSerializable{

    /**
     * @var string
     */
    private $name;
    /**
     * @var string
     */
    private $imageUrl;

    /**
     * @param string
     */
    public function __construct($defImageUrl)
    {
        $this->imageUrl = $defImageUrl;
    }

    public function jsonSerialize() {
        return [
            "name" => $this->name,
            "imageUrl" => $this->imageUrl
        ];
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getImageUrl()
    {
        return $this->imageUrl;
    }

    /**
     * @param mixed $imageUrl
     */
    public function setImageUrl($imageUrl)
    {
        if($imageUrl)
        {
            $this->imageUrl = $imageUrl;
        }
        else
        {
            $this->imageUrl = "/bundles/indigoui/images/empty.png";
        }
    }

}