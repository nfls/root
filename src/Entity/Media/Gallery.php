<?php

namespace App\Entity\Media;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Media\GalleryRepository")
 */
class Gallery
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=150)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=1024)
     */
    private $description;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Media\Photo", mappedBy="gallery")
     */
    private $photos;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Media\Comment", mappedBy="gallery")
     */
    private $comments;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetimetz", options={"default":"CURRENT_TIMESTAMP"})
     */
    private $time;

    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean")
     */
    private $isVisible = false;

    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean")
     */
    private $isPublic = false;

    public function __construct()
    {
        $this->photos = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->time = new \DateTime();
    }

    /**
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @param ArrayCollection $photo
     */
    public function addPhotos($photo)
    {
        $this->photos->add($photo);
    }

    /**
     * @param ArrayCollection $comment
     */
    public function addComments($comment)
    {
        $this->comments->add($comment);
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
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
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @return integer
     */
    public function getPhotoCount(){
        return count($this->photos);
    }

    /**
     * @return integer
     */
    public function getOriginCount(){
        return count($this->photos->filter(function($val){
            return null === $val->getOrigin();
        }));
    }

    /**
     * @return ArrayCollection
     */
    public function getPhotos()
    {
        return $this->photos;
    }

    /**
     * @return ArrayCollection
     */
    public function getComments()
    {
        return $this->comments;
    }

    /**
     * @return \DateTime
     */
    public function getTime()
    {
        return $this->time;
    }

    /**
     * @return bool
     */
    public function isVisible()
    {
        return $this->isVisible;
    }

    /**
     * @return bool
     */
    public function isPublic()
    {
        return $this->isPublic;
    }



    /**
     * @param bool $isVisible
     */
    public function setIsVisible($isVisible)
    {
        $this->isVisible = $isVisible;
    }

    /**
     * @param bool $isPublic
     */
    public function setIsPublic($isPublic)
    {
        $this->isPublic = $isPublic;
    }



}
