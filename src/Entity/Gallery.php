<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\GalleryRepository")
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
     * @ORM\OneToMany(targetEntity="App\Entity\Photo", mappedBy="gallery")
     */
    private $photos;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Comment", mappedBy="gallery")
     */
    private $comments;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetimetz", options={"default":"CURRENT_TIMESTAMP"})
     */
    private $time;

    /**
     * @var string
     *
     * @ORM\Column(type="boolean")
     */
    private $isVisible = false;

    /**
     * @var string
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
    public function GetPhotos()
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
     * @return string
     */
    public function getisVisible()
    {
        return $this->isVisible;
    }

    /**
     * @return string
     */
    public function getisPublic()
    {
        return $this->isPublic;
    }





}
