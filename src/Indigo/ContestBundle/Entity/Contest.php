<?php

namespace Indigo\ContestBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
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

    const OPEN_CONTEST_ID = 1;
    const DEFAULT_SCORE_LIMIT = 10;

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
     * @ORM\Column(name="contest_creation_date", type="datetime", nullable=false)
     */
    private $contestCreationDate;

    /**
     * @var datetime
     *
     * @ORM\Column(name="contest_starting_date", type="datetime", nullable=false)
     */
    private $contestStartingDate;

    /**
     * @var datetime
     *
     * @ORM\Column(name="contest_end_date", type="datetime", nullable=false)
     */
    private $contestEndDate;

    /**
     * @var string
     *
     * @ORM\Column(name="prize_images_paths", type="string", length=255, nullable=true)
     */
    protected $pathForPrizeImage;

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

    private $prizeImage;

    /**
     * @var string
     *
     * @ORM\Column(name="prize", type="string", length=50, nullable=true)
     */
    private $prize;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="Indigo\GameBundle\Entity\Game", mappedBy="contest")
     * @ORM\JoinColumn(referencedColumnName="id")
     */
    private $games;


    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="Indigo\GameBundle\Entity\GameTime", mappedBy="contest")
     * @ORM\JoinColumn(referencedColumnName="id")
     */
    private $gameTimes;

    /**
     * @ORM\Column(name="score_limit", type="smallint", length=2)
     */
    private $scoreLimit;

    /**
     * @param \Datetime $contest_creation_date
     * set value to param $contestCreationDate
     * set value to param $contestType
     */
    public function __construct()
    {
        $this->contestCreationDate = new \DateTime();
        $this->games = new ArrayCollection();
        $this->gameTimes = new ArrayCollection();
        $this->setTableName(1);
        $this->setContestPrivacy(0);
        $this->contestType = true;
        $this->contestStartingDate = new \DateTime();
        $this->contestEndDate = new \DateTime('+1 days');
        $this->setScoreLimit(self::DEFAULT_SCORE_LIMIT);
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

        while (is_file($this->getAbsolutePath($this->pathForImage, "contest")))
            $this->changeImageName();

        $this->getImage()->move(
            $this->getUploadRootDir("contest"),
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
        //return $this->tableName;
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
    public function getPathForPrizeImage()
    {
        return $this->pathForPrizeImage;
    }

    /**
     * @param string $pathForPrizeImage
     */
    public function setPathForPrizeImage($pathForPrizeImage)
    {
        $this->pathForPrizeImage = $pathForPrizeImage;
    }

    /**
     * @return UploadedFile
     */
    public function getPrizeImage()
    {
        return $this->prizeImage;
    }

    /**
     * @param UploadedFile $prizeImage
     */
    public function setPrizeImage(UploadedFile $prizeImage = null)
    {
        $this->prizeImage = $prizeImage;
    }

    public function changePrizeImageName()
    {
        $filename = sha1(uniqid(mt_rand() * mt_rand(), true));
        $this->pathForPrizeImage = $filename . '.' . $this->getPrizeImage()->guessExtension();
    }

    /**
     * @return ArrayCollection
     */
    public function getGames()
    {
        return $this->games;
    }

    /**
     * @param ArrayCollection $games
     */
    public function setGames($games)
    {
        $this->games = $games;
    }

    /**
     * @return ArrayCollection
     */
    public function getGameTimes()
    {
        return $this->gameTimes;
    }

    /**
     * @param ArrayCollection $gameTimes
     */
    public function setGameTimes($gameTimes)
    {
        $this->gameTimes = $gameTimes;
    }

    /**
     * hash contest prise images names
     * function upload contest image
     */
    public function uploadPrizeImage()
    {
        if (null === $this->getPrizeImage()) {
            return;
        }

        $this->changePrizeImageName();

        while (is_file($this->getAbsolutePath($this->pathForPrizeImage, "prizes")))
            $this->changePrizeImageName();

        $this->getPrizeImage()->move(
            $this->getUploadRootDir("prizes"),
            $this->pathForPrizeImage
        );

        $this->prizeImage = null;
    }

    /**
     * @return bool
     */
    public function isContestOpen()
    {
        if ($this->getId() == self::OPEN_CONTEST_ID) {

            return true;
        }

        return false;
    }

    /**
     * @return mixed
     */
    public function getScoreLimit()
    {
        return $this->scoreLimit;
    }

    /**
     * @param $scoreLimit
     * @return $this
     */
    public function setScoreLimit($scoreLimit)
    {
        $this->scoreLimit = (int)$scoreLimit;

        return $this;
    }

    /**
     * @return string
     */
    public function getPrize()
    {
        return $this->prize;
    }

    /**
     * @param string $prize
     */
    public function setPrize($prize)
    {
        $this->prize = $prize;
    }

}