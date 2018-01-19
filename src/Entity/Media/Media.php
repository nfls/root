<?php

namespace App\Entity\Media;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Media\MediaRepository")
 */
class Media
{
    /**
     * @var integer
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\Column(type="smallint")
     */
    private $type;

    /**
     * @var integer
     *
     * @ORM\Column(type="boolean")
     */
    private $isExternal = false;

    /**
     * @var string
     *
     * @ORM\Column(type="string",length=512)
     */
    private $url;



}
