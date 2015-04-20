<?php

namespace Indigo\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Param
 *
 * @ORM\Table(name="params")
 * @ORM\Entity(repositoryClass="Indigo\ApiBundle\Entity\ParamRepository")
 */
class Param
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="param", type="string", length=64, unique=true)
     */
    private $param;

    /**
     * @var string
     *
     * @ORM\Column(name="value", type="string", length=255)
     */
    private $value;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set param
     *
     * @param string $param
     * @return Param
     */
    public function setParam($param)
    {
        $this->param = $param;

        return $this;
    }

    /**
     * Get param
     *
     * @return string 
     */
    public function getParam()
    {
        return $this->param;
    }

    /**
     * Set value
     *
     * @param string $value
     * @return Param
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get value
     *
     * @return string 
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param $param
     * @param $value
     */
    public function setParamAndValue($param, $value)
    {
        $this->setParam($param);
        $this->setValue($value);
    }
}
