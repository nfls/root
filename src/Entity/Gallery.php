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


}
