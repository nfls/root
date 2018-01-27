<?php

namespace App\Entity;

use App\Entity\User\User;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidType;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AlumniRepository")
 */
class Alumni
{
    /**
     * @var UuidType
     *
     * @ORM\Id
     * @ORM\Column(type="uuid", unique=true)
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class="Ramsey\Uuid\Doctrine\UuidGenerator")
     */
    private $id;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\User\User",inversedBy="authTickets")
     */
    private $user;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetimetz")
     */
    private $submitTime;

    /**
     * @var boolean
     *
     * @ORM\Column(type="smallint")
     */
    private $status;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    private $chineseName;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    private $englishName;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="date")
     */
    private $birthday;

    /**
     * @var integer
     *
     * @ORM\Column(type="smallint")
     */
    private $gender;

    /**
     * @var integer
     *
     * @ORM\Column(type="smallint")
     */
    private $juniorSchool;

    /**
     * @var integer
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    private $juniorRegistration;

    /**
     * @var integer
     *
     * @ORM\Column(type="smallint", nullable=true)
     */
    private $juniorClass;

     /**
      * @var integer
      *
      * @ORM\Column(type="smallint")
      */
    private $seniorSchool;

    /**
     * @var integer
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    private $seniorRegistration;

    /**
     * @var integer
     *
     * @ORM\Column(type="smallint", nullable=true)
     */
    private $seniorClass;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $university;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $major;

    /**
     * @var string
     *
     * @ORM\Column(type="string",length=500, nullable=true)
     */
    private $workInfo;

    /**
     * @var string
     *
     * @ORM\Column(type="string",length=500, nullable=true)
     */
    private $personalInfo;

    /**
     * @var string
     *
     * @ORM\Column(type="string",length=100)
     */
    private $onlineContact;

    /**
     * @var string
     *
     * @ORM\Column(type="json",length=256)
     */
    private $location;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=300, nullable=true)
     */
    private $remark;

    public function __construct()
    {
        $this->status = 0;
    }
}
