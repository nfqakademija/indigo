<?php
/**
 * Created by PhpStorm.
 * User: Tadas
 * Date: 2015-03-23
 * Time: 16:41
 */

namespace Indigo\ContestBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;


/**
 * Order
 *
 * @ORM\Table(name="contests")
 * @ORM\Entity(repositoryClass="Indigo\ContestBundle\Entity\DataRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Data {

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false, options={"unsigned":true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(name="user_id", type="integer", nullable=true, options={"unsigned":true})
     */
    private $user_id;

    /**
     * @var string
     *
     * @Assert\NotBlank(groups={"Default"})
     * @ORM\Column(name="contest_title", type="string", length=30, nullable=false)
     */
    private $contest_title;

    /**
     * @var string
     *
     * @ORM\Column(name="image_path", type="string", length=255, nullable=false)
     */
    public $path_for_image;

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
    private $table_name;

    /**
     * @var smallint
     *
     * @ORM\Column(name="contest_privacy", type="smallint", nullable=false)
     */
    private $contest_privacy;

    /**
     * @var smallint
     *
     * 0 = single, 1 = team
     *
     * @ORM\Column(name="contest_type", type="smallint", nullable=false)
     */
    private $contest_type;

    /**
     * @var \Datetime
     *
     * @ORM\Column(name="contest_creation_date", type="datetime", nullable=false)
     */
    private $contest_creation_date;

    /**
     * @var datetime
     *
     * @ORM\Column(name="contest_starting_date", type="datetime", nullable=true)
     */
    private $contest_starting_date;

    /**
     * @var datetime
     *
     * @ORM\Column(name="contest_end_date", type="datetime", nullable=true)
     */
    private $contest_end_date;

    /**
     * @return int
     */
    public function getId(){
        return $this->id;
    }

    /**
     * @return string
     */
    public function getContestTitle(){
        return $this->contest_title;
    }

    /**
     * @return string
     */

    public function getAbsolutePath(){
        return null === $this->path_for_image ? null : $this->getUploadRootDir().'/'.$this->path_for_image;
    }

    public function getWebPath(){
       return null === $this->path_for_image ? null : $this->getUploadDir().'/'.$this->path_for_image;
    }

    protected function getUploadRootDir(){
        return __DIR__.'/../../../../web/'.$this->getUploadDir();
    }

    protected function getUploadDir(){
        return 'uploaded_images/contest';
    }

    /**
     * @return UploadedFile
     */
    public function getImage(){
        return $this->image;
    }

    /**
     * @return string
     */
    public function getTableName(){
        return $this->table_name;
    }

    /**
     * @return smallint
     */
    public function getContestPrivacy(){
        return $this->contest_privacy;
    }

    /**
     * @return smallint
     */
    public function getContestType(){
        return $this->contest_type;
    }

    /**
     * @param int $user_id
     */
    public function setUserId($user_id){
        $this->user_id = $user_id;
    }

    /**
    * @param string $contest_title
    */
    public function setContestTitle($contest_title){
        $this->contest_title = $contest_title;
    }

    /**
     * @param UploadedFile $image
     */
    public function setImage(UploadedFile $image = null){
        $this->image = $image;
    }

    /**
     * @param string $table_name
     */
    public function setTableName($table_name){
        $this->table_name = $table_name;
    }

    /**
     * @param smallint $contest_privacy
     */
    public function setContestPrivacy($contest_privacy){
        $this->contest_privacy = $contest_privacy;
    }

    /**
     * @param smallint $contest_type
     */
    public function setContestType($contest_type){
        $this->contest_type = $contest_type;
    }

    public function changeImageName(){
        $filename = sha1(uniqid(mt_rand()*mt_rand(), true));
        $this->path_for_image = $filename.'.'.$this->getImage()->guessExtension();
    }

    /**
     * function upload contest image
     */
    public function uploadImage(){
        if(null === $this->getImage()) {
            return;
        }

        $this->changeImageName();

        while(is_file($this->getAbsolutePath()))
            $this->changeImageName();

        $this->getImage()->move(
            $this->getUploadRootDir(),
            $this->path_for_image
        );

        $this->image = null;
    }


    /**
     * @param \Datetime $contest_creation_date
     */
    public function __construct(){
        $this->contest_creation_date = new \DateTime();
    }
    /**
     * @param datetime $contest_starting_date
     */
    public function setContestStartDate($contest_starting_date){
        $this->contest_starting_date = $contest_starting_date;
    }

    /**
     * @param datetime $contest_end_date
     */
    public function setContestEndDate($contest_end_date){
        $this->contest_end_date = $contest_end_date;
    }


}