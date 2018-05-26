<?php

namespace App\Entity\User;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\User\DeviceRepository")
 */
class Device
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
     * @ORM\Column(type="integer")
     */
    private $type;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    private $token;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=1024)
     */
    private $model;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\User\User")
     */
    private $user;

    // add your own fields
}
