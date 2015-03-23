<?php

namespace Indigo\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder;

/**
 * User
 * @ORM\Table(name="users")
 * @ORM\Entity(repositoryClass="Indigo\UserBundle\Entity\UserRepository")
 */
class User extends MessageDigestPasswordEncoder
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", unique=true)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var isActive
     *
     * @ORM\Column(name="active", options={"default" : 1})
     */
    private $isActive;

    /**
     * @var username
     *
     * @ORM\Column(type="string", length=35, unique=true)
     */
    private $username;

    /**
     * @var email
     *
     * @ORM\Column(type="string", length=60, unique=true)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(length=32)
     */
    private $password;

    /**
     * @var salt
     *
     * @ORM\Column(type="string", length=40)
     */
    private $salt;

    /**
     * @var picture
     *
     * @ORM\Column(name="picture", type="string", length=255, nullable=true)
     */
    private $picture;

     //*
     //* @var UserProfile
     //*
     //* @ORM\OneToOne(targetEntity="UserProfile", mappedBy="user", cascade={"persist", "remove"})
     //*
     //* @Assert\Valid()
     //*
    //private $profile;

    /**
     * @ORM\Column(name="registration_date", type="datetime")
     */
    private $registrationDate;

    public function __construct()
    {
        $this->isActive = true;
        $this->salt = md5(uniqid(null, true));
        $this->registrationDate = new \DateTime();
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
     * Set isActive
     *
     * @param boolean $isActive
     * @return User
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * Get isActive
     *
     * @return boolean
     */
    public function getIsActive()
    {
        return $this->isActive;
    }

    /**
     * Set username
     *
     * @param string $username
     * @return SignIn
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get username
     *
     * @return string 
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return SignIn
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set password
     *
     * @param string $password
     * @return SignIn
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get password
     *
     * @return string 
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Returns the salt that was originally used to encode the password.
     *
     * This can return null if the password was not encoded using a salt.
     *
     * @return string|null The salt
     */
    public function getSalt()
    {
        return $this->salt;
    }

    /**
     * Set salt
     *
     * @param string $salt
     * @return User
     */
    public function setSalt($salt)
    {
        $this->salt = $salt;

        return $this;
    }

    /**
     * Set picture
     *
     * @param string $picture
     * @return User
     */
    public function setPicture($picture)
    {
        $this->picture = $picture;

        return $this;
    }

    /**
     * Get picture
     *
     * @return string
     */
    public function getPicture()
    {
        return $this->picture;
    }
}
