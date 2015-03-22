<?php
/**
 * Created by PhpStorm.
 * User: Tadas
 * Date: 2015-03-22
 * Time: 20:43
 */

namespace Indigo\LocationsRegBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Order
 *
 * @ORM\Table(
 *     name="locations",
 *     indexes={
 *         @ORM\Index(name="active_idx", columns={"active"})
 *      }
 * )
 * @ORM\Entity(repositoryClass="Indigo\LocationsRegBundle\Entity\LocationRepository")
 */
class Location
{
    const STATE_ACTIVE = 1;

    const STATE_INACTIVE = 0;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false, options={"unsigned":true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="active", type="integer", nullable=false)
     */
    private $active;

    /**
     * @var string
     *
     * @Assert\NotBlank(groups={"Default"})
     * @Assert\Length(min=6)
     * @ORM\Column(name="title", type="string", length=120, nullable=false)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="address", type="string", length=255, nullable=false)
     */
    private $address;

    public function __construct()
    {
        $this->active = self::STATE_INACTIVE;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param int $active
     */
    public function setActive($active)
    {
        $this->active = $active;
    }

    /**
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @param string $address
     */
    public function setAddress($address)
    {
        $this->address = $address;
    }
}