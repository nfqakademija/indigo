<?php
/**
 * Created by PhpStorm.
 * User: Tadas
 * Date: 2015-03-23
 * Time: 16:41
 */

namespace Indigo\ContestBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Indigo\UserBundle\Entity\User;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;


/**
 * Order
 *
 * @ORM\Table(name="contests")
 * @ORM\Entity(repositoryClass="Indigo\ContestBundle\Entity\ContestRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Contest
{

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false, options={"unsigned":true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var User $user
     *
     * @ORM\ManyToOne(targetEntity="Indigo\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="SET NULL")
     */
    private $user;

    /**
     * @var string
     *
     * @Assert\NotBlank(groups={"Default"})
     * @ORM\Column(name="contest_title", type="string", length=30, nullable=false)
     */
    private $contestTitle;

    /**
     * @var string
     *
     * @ORM\Column(name="image_path", type="string", length=255, nullable=true)
     */
    protected $pathForImage;

    /**
     * @Assert\File(maxSize="2M", mimeTypes={"image/jpg", "image/jpeg", "image/gif", "image/png"})
     * @Assert\Image(
     *  minWidth = 100,
     *  maxWidth = 500,
     *  minHeight = 100,
     *  maxHeight = 500,
     *  allowLandscape = false,
     *  allowPortrait = false,
     *  allowSquare = true)
     */

    private $image;

    /**
     * @var string
     *
     * @Assert\NotBlank(groups={"Default"})
     * @ORM\Column(name="table_name", type="string", length=30, nullable=false)
     */
    private $tableName;

    /**
     * @var bool
     *
     * @ORM\Column(name="contest_privacy", type="boolean", nullable=false)
     */
    private $contestPrivacy;

    /**
     * @var bool
     *
     * @ORM\Column(name="contest_type", type="boolean", nullable=false)
     */
    private $contestType;

    /**
     * @var \Datetime
     *
     * @ORM\Column(name="contest_creation_date", type="datetime", nullable=true)
     */
    private $contestCreationDate;

    /**
     * @var datetime
     *
     * @ORM\Column(name="contest_starting_date", type="datetime", nullable=true)
     */
    private $contestStartingDate;

    /**
     * @var datetime
     *
     * @ORM\Column(name="contest_end_date", type="datetime", nullable=true)
     */
    private $contestEndDate;

    /**
     * @var string
     *
     * @ORM\Column(name="prise_images_paths", type="string", length=255, nullable=true)
     */
    protected $pathForPriseImages;

    /**
     * @Assert\File(maxSize="3M", mimeTypes={"image/jpg", "image/jpeg", "image/gif", "image/png"})
     * @Assert\Image(
     *  minWidth = 500,
     *  maxWidth = 1500,
     *  minHeight = 500,
     *  maxHeight = 1500,
     *  allowLandscape = true,
     *  allowPortrait = true,
     *  allowSquare = true)
     */

    private $priseImages;


    /**
     * @param \Datetime $contest_creation_date
     */
    public function __construct()
    {
        $this->contestCreationDate = new \DateTime();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getContestTitle()
    {
        return $this->contestTitle;
    }

    /**
     * @return string
     */
    public function setContestTitle($contestTitle)
    {
        return $this->contestTitle = $contestTitle;
    }

    /**
     * @return string
     */
    public function getPathForImage()
    {
        return $this->pathForImage;
    }

    /**
     * @param string $pathForImage
     */
    public function setPathForImage($pathForImage)
    {
        $this->pathForImage = $pathForImage;
    }

    /**
     * @return string
     */

    public function getAbsolutePath($imagePath)
    {
        return null === $imagePath ? null : $this->getUploadRootDir() . '/' . $imagePath;
    }

    public function getWebPath($imagePath)
    {
        return null === $imagePath ? null : $this->getUploadDir() . '/' . $imagePath;
    }

    protected function getUploadRootDir()
    {
        return __DIR__ . '/../../../../web/' . $this->getUploadDir();
    }

    protected function getUploadDir()
    {
        return 'uploads/contest';
    }

    /**
     * @return UploadedFile
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * @param UploadedFile $image
     */
    public function setImage(UploadedFile $image = null)
    {
        $this->image = $image;
    }

    public function changeImageName()
    {
        $filename = sha1(uniqid(mt_rand() * mt_rand(), true));
        $this->pathForImage = $filename . '.' . $this->getImage()->guessExtension();
    }

    /**
     * function upload contest image
     */
    public function uploadImage()
    {
        if (null === $this->getImage()) {
            return;
        }

        $this->changeImageName();

        while (is_file($this->getAbsolutePath($this->pathForImage)))
            $this->changeImageName();

        $this->getImage()->move(
            $this->getUploadRootDir(),
            $this->pathForImage
        );

        $this->image = null;
    }

    /**
     * @return int
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param int $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }


    /**
     * @return string
     */
    public function getTableName()
    {
        return $this->tableName;
    }

    /**
     * @param string $tableName
     */
    public function setTableName($tableName)
    {
        $this->tableName = $tableName;
    }

    /**
     * @return boolean
     */
    public function isContestPrivacy()
    {
        return $this->contestPrivacy;
    }

    /**
     * @param boolean $contestPrivacy
     */
    public function setContestPrivacy($contestPrivacy)
    {
        $this->contestPrivacy = $contestPrivacy;
    }

    /**
     * @return boolean
     */
    public function isContestType()
    {
        return $this->contestType;
    }

    /**
     * @param boolean $contestType
     */
    public function setContestType($contestType)
    {
        $this->contestType = $contestType;
    }

    /**
     * @return \Datetime
     */
    public function getContestCreationDate()
    {
        return $this->contestCreationDate;
    }

    /**
     * @param \Datetime $contestCreationDate
     */
    public function setContestCreationDate($contestCreationDate)
    {
        $this->contestCreationDate = $contestCreationDate;
    }

    /**
     * @return datetime
     */
    public function getContestStartingDate()
    {
        return $this->contestStartingDate;
    }

    /**
     * @param datetime $contestStartingDate
     */
    public function setContestStartingDate($contestStartingDate)
    {
        $this->contestStartingDate = $contestStartingDate;
    }

    /**
     * @return datetime
     */
    public function getContestEndDate()
    {
        return $this->contestEndDate;
    }

    /**
     * @param datetime $contestEndDate
     */
    public function setContestEndDate($contestEndDate)
    {
        $this->contestEndDate = $contestEndDate;
    }

    /**
     * @return string
     */
    public function getPathForPriseImages()
    {
        return $this->pathForPriseImages;
    }

    /**
     * @param string $pathForPriseImages
     */
    public function setPathForPriseImages($pathForPriseImages)
    {
        $this->pathForPriseImages = $pathForPriseImages;
    }

    /**
     * @return UploadedFile
     */
    public function getPriseImages()
    {
        return $this->priseImages;
    }

    /**
     * @param UploadedFile $priseImages
     */
    public function setPriseImages(UploadedFile $priseImages = null)
    {
        $this->priseImages = $priseImages;
    }

}