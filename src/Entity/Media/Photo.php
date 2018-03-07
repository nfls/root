<?php

namespace App\Entity\Media;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Media\PhotoRepository")
 */
class Photo
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var Gallery
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Media\Gallery", inversedBy="photos")
     */
    private $gallery;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=256)
     */
    private $thumb;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=256)
     */
    private $hd;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    private $width;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    private $height;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=256, nullable=true)
     */
    private $origin;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", options={"default":"CURRENT_TIMESTAMP"})
     */
    private $time;

    /**
     * @var string
     *
     * @ORM\Column(type="json", nullable=true)
     */

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

    /**
     * @var array
     *
     * @ORM\Column(type="json", nullable=true)
     */
    private $faces;


    public function __construct()
    {
        $this->time = new \DateTime();
    }

    /**
     * @return Gallery
     */
    //public function getGalleryEntity()
    //{
    //    return $this->gallery;
    //}

    /**
     * @return string
     */
    public function getGallery()
    {
        if (@is_null(@$this->gallery))
            return null;
        else
            return $this->gallery->getTitle();
    }

    /**
     * @param Gallery $gallery
     */
    public function setGallery($gallery)
    {
        $this->gallery = $gallery;
    }

    /**
     * @return string
     */
    public function getThumb()
    {
        return $this->thumb;
    }

    /**
     * @param string $thumb
     */
    public function setThumb($thumb)
    {
        $this->thumb = $thumb;
    }

    /**
     * @return string
     */
    public function getHd()
    {
        return $this->hd;
    }

    /**
     * @param string $hd
     */
    public function setHd($hd)
    {
        $this->hd = $hd;
    }

    /**
     * @return string
     */
    public function getOrigin()
    {
        return $this->origin;
    }

    /**
     * @param string $origin
     */
    public function setOrigin($origin)
    {
        $this->origin = $origin;
    }


    /**
     * @return bool
     */
    public function isVisible()
    {
        return $this->isVisible;
    }

    /**
     * @param bool $isVisible
     */
    public function setIsVisible($isVisible)
    {
        $this->isVisible = $isVisible;
    }

    /**
     * @return bool
     */
    public function isPublic()
    {
        return $this->isPublic;
    }

    /**
     * @param bool $isPublic
     */
    public function setIsPublic($isPublic)
    {
        $this->isPublic = $isPublic;
    }

    /**
     * @return mixed
     */
    public function getTime()
    {
        return $this->time;
    }

    /**
     * @param mixed $time
     */
    public function setTime($time)
    {
        $this->time = $time;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * @param int $width
     */
    public function setWidth($width)
    {
        $this->width = $width;
    }

    /**
     * @return int
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * @param int $height
     */
    public function setHeight($height)
    {
        $this->height = $height;
    }


    /**
     * @return array
     */
    public function getFaces(): ?array
    {
        return $this->faces;
    }


    // add your own fields
}
