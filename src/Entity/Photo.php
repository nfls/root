<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PhotoRepository")
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
     * @ORM\ManyToOne(targetEntity="App\Entity\Gallery", inversedBy="photos")
     */
    private $gallery;

    /**
     * @var boolean
     *
     * @ORM\Column(type="string", length=256)
     */
    private $thumb;

    /**
     * @var boolean
     *
     * @ORM\Column(type="string", length=256)
     */
    private $hd;

    /**
     * @var boolean
     *
     * @ORM\Column(type="string", length=256, nullable=true)
     */
    private $origin;

    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean")
     */
    private $isVisible = true;

    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean")
     */
    private $isPublic = false;

    /**
     * @return Gallery
     */
    public function getGallery()
    {
        return $this->gallery;
    }

    /**
     * @param Gallery $gallery
     */
    public function setGallery($gallery)
    {
        $this->gallery = $gallery;
    }

    /**
     * @return bool
     */
    public function isThumb()
    {
        return $this->thumb;
    }

    /**
     * @param bool $thumb
     */
    public function setThumb($thumb)
    {
        $this->thumb = $thumb;
    }

    /**
     * @return bool
     */
    public function isHd()
    {
        return $this->hd;
    }

    /**
     * @param bool $hd
     */
    public function setHd($hd)
    {
        $this->hd = $hd;
    }

    /**
     * @return bool
     */
    public function isOrigin()
    {
        return $this->origin;
    }

    /**
     * @param bool $origin
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



    // add your own fields
}
