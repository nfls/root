<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\LaunchScreenRepository")
 */
class LaunchScreen
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
     * @ORM\Column(type="string", length=1024)
     */
    private $text;

    /**
     * @ORM\Column(type="string", length=1024)
     */
    private $portrait_url;

    /**
     * @ORM\Column(type="string", length=1024)
     */
    private $landscape_url;


    // add your own fields
}
