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



    // add your own fields
}
