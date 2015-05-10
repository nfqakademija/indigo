<?php

namespace Indigo\UserBundle\Entity;


use Doctrine\ORM\Mapping as ORM;
use Indigo\GameBundle\Entity\PlayerTeamRelation;
use Symfony\Component\HttpFoundation\File\UploadedFile;
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

    const ANONYMOUS_EMAIL_DOMAIN = 'example.com';
    const ANONYMOUS_USERNAME = 'anonymous';
    const ANONYMOUS_PASSWORD = 'indigo';
    const ANONYMOUS_FACE = '/bundles/indigoui/images/anonymous.png';
    const NO_FACE = '/bundles/indigoui/images/empty.png';
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer",  options={"unsigned":true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="active", type="smallint", options={"default" : 1})
     */
    private $isActive;

    /**
     * @ORM\Column(name="locked", type="boolean", options={"default":0})
     */
    private $isLocked;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=25, unique=true, nullable=true)
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
     * @ORM\ManyToMany(targetEntity="Role", inversedBy="users", cascade={"persist"})
     * @ORM\JoinTable(name="user_role",)
     */
    private $roles;


    /**
    * @ORM\OneToOne(targetEntity="ResetPassword", mappedBy="user")
    *
    */
    private $reset_password_hash;

    /**
     * @var string
     *
     * @ORM\Column(name="picture", type="string", length=255, nullable=true)
     */
    protected $picture;

    /**
     * @Assert\File(maxSize="3M", mimeTypes={"image/jpg", "image/jpeg", "image/gif", "image/png"})
     * @Assert\Image(
     *  minWidth = 100,
     *  maxWidth = 1024,
     *  minHeight = 100,
     *  maxHeight = 1024,
     *  allowLandscape = true,
     *  allowPortrait = true,
     *  allowSquare = true)
     */

    private $pictureFile;


    /**
     * @ORM\Column(name="registration_date", type="datetime")
     */
    private $registrationDate;

    /**
     * @ORM\Column(name="card_id", type="integer", length=7, nullable=true, options={"unsigned":true})
     * @Assert\Length(min=7, minMessage="user.min_cardid_length", max=7, maxMessage="user.max_cardid_length");
     */
    private $cardId;

    /**
     * @var ArrayCollection(<PlayerTeamRelation>)
     * @ORM\OneToMany(targetEntity="Indigo\GameBundle\Entity\PlayerTeamRelation", mappedBy="player", cascade={"persist"})
     */
    private $teams;

    /**
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=true)
     */
    private $name;


    public function __construct()
    {
        $this->isActive = 1;
        $this->isLocked = 0;
        $this->salt = md5(uniqid(null, true));
        $this->roles = new ArrayCollection();
        $this->teams = new ArrayCollection();
        $this->registrationDate = new \DateTime('now');
        $this->picture = self::ANONYMOUS_FACE;
    }

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
     * @param integer $isActive
     * @return User
     */
    public function setIsActive($isActive)
    {
        $this->isActive = (int)$isActive;

        return $this;
    }

    /**
     * Get isActive
     *
     * @return integer
     */
    public function getIsActive()
    {
        return (int)$this->isActive;
    }

    public function setIsLocked($isLocked)
    {
        $this->isLocked = (int)$isLocked;

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
     * @return string
     */
    public function getPicture()
    {
        return $this->picture;
    }

    /**
     * @param string $picture
     */
    public function setPicture($picture)
    {
        $this->picture = $picture;

        return $this;
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
        return $this->roles->toArray();
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
     * @return UploadedFile
     */
    public function getPictureFile()
    {
        return $this->pictureFile;
    }

    /**
     * @param UploadedFile $pictureFile
     */
    public function setPictureFile(UploadedFile $pictureFile)
    {
        $this->pictureFile = $pictureFile;
    }

    /**
     * @return string
     */

    public function getAbsolutePath($imagePath, $dir)
    {
        return null === $imagePath ? null : $this->getUploadRootDir($dir) . '/' . $imagePath;
    }

    public function getWebPath($imagePath, $dir)
    {
        return null === $imagePath ? null : $this->getUploadDir($dir) . '/' . $imagePath;
    }

    protected function getUploadRootDir($dir)
    {
        return __DIR__ . '/../../../../web/' . $this->getUploadDir($dir);
    }

    protected function getUploadDir($dir)
    {
        return 'uploads/'.$dir;
    }

    public function changePictureName()
    {
        $filename = sha1(uniqid(mt_rand() * mt_rand(), true));
        return $filename . '.' . $this->getPictureFile()->guessExtension();
    }

    /**
     * function upload contest image
     */
    public function uploadPicture()
    {
        if (null === $this->getPicture()) {
            return;
        }

        $pictureName = $this->changePictureName();
        while (is_file($this->getAbsolutePath($pictureName, "profiles_pictures")))
            $this->changePictureName();

        $this->getPictureFile()->move(
            $this->getUploadRootDir("profiles_pictures"),
            $pictureName
        );

        $this->pictureFile = null;
        $this->setPicture('/' . $this->getWebPath($pictureName, "profiles_pictures"));
    }



}
