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
     * @var string
     *
     * @Assert\NotBlank(groups={"Default"})
     * @ORM\Column(name="contest_title", type="string", length=30, nullable=false)
     */
    private $contest_title;

    /**
     * @var string
     *
     * @ORM\Column(name="image_path", type="string", length=255, nullable=true)
     */
    public $path_for_image;

    /**
     * @Assert\File(maxSize="2000000")
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
    public function getPathForImage(){
        return $this->getUploadDir().$this->path_for_image;
    }

    public function getUploadDir(){
        return __DIR__.'/../../../../web/uploaded_images/contest/';
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


    public function uploadImage(){
        if (null === $this->getImage()) {
            return;
        }

        $this->getImage()->move(
            $this->getPathForImage(),
            $this->getImage()->getClientOriginalName()
        );

        $this->path_for_image = $this->getImage()->getClientOriginalName();

        $this->image = null;
    }

}