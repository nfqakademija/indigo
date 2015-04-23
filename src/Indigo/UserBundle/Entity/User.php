<?php

namespace Indigo\UserBundle\Entity;


use Doctrine\ORM\Mapping as ORM;
use Indigo\GameBundle\Entity\PlayerTeamRelation;
use Symfony\Component\Validator\Constraints as Assert;

use Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;

/**
 * User
 * @ORM\Table(name="users")
 * @UniqueEntity(fields={"username"})
 * @UniqueEntity(fields={"email"})
 * @ORM\Entity(repositoryClass="Indigo\UserBundle\Entity\UserRepository")
 */
class User extends MessageDigestPasswordEncoder implements AdvancedUserInterface, \Serializable
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
     * @var integer
     *
     * @ORM\Column(name="active", options={"default" : 1})
     */
    private $isActive;

    /**
     * @ORM\Column(name="locked", type="boolean", options={"default":0})
     */
    private $isLocked;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=25, unique=true)
     */
    private $username;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=60, unique=true)
     * @Assert\Email(groups={"Default", "Profile"}, checkMX="true", message="user.error.username_must_be_email")
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(length=64)
     * @Assert\Length(groups={"Default", "Profile"}, min=4, minMessage="user.error.password_to_short")
     */
    private $password;

    /**
     *
     * @ORM\Column(type="string", length=64)
     */
    private $salt;

    /**
     * @ORM\ManyToMany(targetEntity="Role", inversedBy="users")
     * @ORM\JoinTable(name="user_role")
     */
    private $roles;


    /**
    * @ORM\OneToOne(targetEntity="ResetPassword", mappedBy="user")
    *
    */
    private $reset_password_hash;

    /**
     *
     * @ORM\Column(name="picture", type="string", length=255, nullable=true)
     */
    private $picture;


    /**
     * @ORM\Column(name="registration_date", type="datetime")
     */
    private $registrationDate;

    /**
     * @ORM\Column(name="card_id")
     */
    private $cardId;

    /**
     * @var ArrayCollection(<PlayerTeamRelation>)
     * @ORM\OneToMany(targetEntity="Indigo\GameBundle\Entity\PlayerTeamRelation", mappedBy="player", cascade={"persist"})
     */
    private $teams;

    public function __toString()
    {
        return (string)$this->getId();
    }

    /**
     * @return ArrayCollection
     */
    public function getTeams()
    {
        return $this->teams;
    }

    /**
     * @param $teams
     * @return $this
     */
    public function setTeams($teams)
    {
        $this->teams = $teams;
        return $this;
    }

    public function addTeam(PlayerTeamRelation $team)
    {
        $this->teams->add($team);
    }


    /**
     * @return mixed
     */
    public function getCardId()
    {
        return $this->cardId;
    }

    /**
     * @param mixed $cardId
     */
    public function setCardId($cardId)
    {
        $this->cardId = $cardId;
    }

    public function __construct()
    {
        $this->isActive = true;
        $this->isLocked = false;
        $this->salt = md5(uniqid(null, true));
        $this->roles = new ArrayCollection();
        $this->registrationDate = new \DateTime('now');
        $this->teams = new ArrayCollection();
    }


    /**
     * @return bool
     */
    public function isAccountNonExpired()
    {
        return true;
    }

    /**
     * @return bool
     */
    public function isAccountNonLocked()
    {
        return !$this->getIsLocked();
    }

    /**
     * @return bool
     */
    public function isCredentialsNonExpired()
    {
        return true;
    }

    /**
     *
     */
    public function isEnabled()
    {
        return $this->getIsActive();
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
        return (bool)$this->isActive;
    }

    public function setIsLocked($isLocked)
    {
        $this->isLocked = $isLocked;

        return $this;
    }

    /**
     * Get isLocked
     *
     * @return boolean
     */
    public function getIsLocked()
    {
        return $this->isLocked;
    }

    /**
     * Set username
     *
     * @param string $username
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Returns the username used to authenticate the user.
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
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returns the password used to authenticate the user.
     * This should be the encoded password. On authentication, a plain-text password will be salted, encoded, and then compared to this value.
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


    /**
     *
     */
    public function cryptPassword() {
        $this->password = md5($this->password);
    }


    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * String representation of object
     * @link http://php.net/manual/en/serializable.serialize.php
     * @return string the string representation of the object or null
     */
    public function serialize()
    {
        return serialize(
            array(
                $this->id
            )
        );
    }

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * Constructs the object
     * @link http://php.net/manual/en/serializable.unserialize.php
     * @param string $serialized <p>
     * The string representation of the object.
     * </p>
     * @return void
     */
    public function unserialize($serialized)
    {
        list (
            $this->id
            ) = unserialize($serialized);
    }

    /**

     */
    public function getRoles()
    {
        return array('ROLE_USER');
        //return $this->roles->toArray();
    }

    /**
     * @param $roles
     */
    public function setRoles($roles)
    {
        $this->roles = $roles;
    }

    /**
     * @return string
     */
    public function getRolesText()
    {
        $rolesText = '';
        $roles = $this->getRoles();
        if (is_array($roles)) {
            foreach ($roles as $role) {
                $rolesText .= $role->getName() . ', ';
            }
        }

        $rolesText = trim($rolesText, ', ');

        return $rolesText;
    }

     /**
     * Removes sensitive data from the user.
     *
     * This is important if, at any given point, sensitive information like
     * the plain-text password is stored on this object.
     */
    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }
    /**
     * Set role
     *
     * @param string $role
     * @return User
     */
    public function setRole($role)
    {
        $this->role = $role;

        return $this;
    }


    /**
     * Check is user has administrative roles
     *
     * @return bool
     */
    public function hasAdministrationRole()
    {
        $criteria = Criteria::create()
            ->where(Criteria::expr()->eq('role', 'ROLE_ADMIN'))
            ->orWhere(Criteria::expr()->eq('role', 'ROLE_SUPER_ADMIN'))
            ->orWhere(Criteria::expr()->eq('role', 'ROLE_MODERATOR'));

        return count($this->roles->matching($criteria)) > 0;
    }

    /**
     * Check if user is moderator
     *
     * @return bool
     */
    public function isModerator()
    {
        $criteria = Criteria::create()->where(Criteria::expr()->eq('role', 'ROLE_MODERATOR'));

        return count($this->roles->matching($criteria)) > 0;
    }

    /**
     * Check if user is admin
     *
     * @return bool
     */
    public function isAdmin()
    {
        $criteria = Criteria::create()->where(Criteria::expr()->eq('role', 'ROLE_ADMIN'));

        return count($this->roles->matching($criteria)) > 0;
    }

    /**
     * Add roles
     *
     * @param \Indigo\UserBundle\Entity\Role $roles
     * @return User
     */
    public function addRole(\Indigo\UserBundle\Entity\Role $roles)
    {
        $this->roles[] = $roles;

        return $this;
    }

    /**
     * Remove roles
     *
     * @param \Indigo\UserBundle\Entity\Role $roles
     */
    public function removeRole(\Indigo\UserBundle\Entity\Role $roles)
    {
        $this->roles->removeElement($roles);
    }


    /**
     * Set registrationDate
     *
     * @param \DateTime $registrationDate
     * @return User
     */
    public function setRegistrationDate($registrationDate)
    {
        $this->registrationDate = $registrationDate;

        return $this;
    }

    /**
     * Get registrationDate
     *
     * @return \DateTime 
     */
    public function getRegistrationDate()
    {
        return $this->registrationDate;
    }

    /**
     * @return mixed
     */
    public function getResetPasswordHash()
    {
        return $this->reset_password_hash;
    }

    /**
     * @param mixed $reset_password_hash
     */
    public function setResetPasswordHash($reset_password_hash)
    {
        $this->reset_password_hash = $reset_password_hash;
    }



}
