<?php

namespace Indigo\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;


/**
 * ResetPassword
 *
 * @ORM\Table(name="reset_password")
 * @ORM\Entity(repositoryClass="Indigo\UserBundle\Entity\ResetPasswordRepository")
 */
class ResetPassword
{

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", options={"unsigned":true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

   /**
    * @ORM\OneToOne(targetEntity="User", inversedBy="reset_password_hash")
    * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
    */
    private $user;


    /**
     * @var string
     *
     * @ORM\Column(name="hash", type="string", length=50)
     */
    private $hash;

    /**
     * @var integer
     *
     * @ORM\Column(name="active", type="integer", options={"default":1})
     */
    private $active;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="expires", type="datetime")
     */
    private $expires;



    function __construct() {
        $this->expires = new \DateTime('+14 days');
    }


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
     * Set user
     */
    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set hash
     */
    public function setHash($hash)
    {
        $this->hash = $hash;

        return $this;
    }

    /**
     * Get hash
     *
     * @return string 
     */
    public function getHash()
    {
        return $this->hash;
    }

    /**
     * Set active
     *
     * @param integer $active
     * @return ResetPassword
     */
    public function setActive($active)
    {
        $this->active = $active;

        return $this;
    }

    /**
     * Get active
     *
     * @return integer 
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * Set expires
     *
     * @param \DateTime $expires
     * @return ResetPassword
     */
    public function setExpires($expires)
    {
        $this->expires = $expires;

        return $this;
    }

    /**
     * Get expires
     *
     * @return \DateTime 
     */
    public function getExpires()
    {
        return $this->expires;
    }
}
