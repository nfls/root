<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\MessageRepository")
 */
class Message
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetimetz")
     */
    private $time;

    /**
     * @ORM\Column(type="integer")
     */
    private $group;

    /**
     * @ORM\Column(type="integer")
     */
    private $type;

    /**
     * @ORM\Column(type="integer")
     */
    private $place;

    /**
     * @ORM\Column(type="string", length=1024)
     */
    private $title;

    /**
     * @ORM\Column(type="string", length=1024)
     */
    private $detail;

    /**
     * @ORM\Column(type="string", length=1024)
     */
    private $image;

    /**
     * @ORM\Column(type="string", length=1024)
     */
    private $url;

    /**
     * @ORM\Column(type="integer")
     */
    private $priority;

    public function __construct()
    {
        $this->priority = 1;
        $this->time = new \DateTime();
    }
}
