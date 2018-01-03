<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AlumniRepository")
 */
class Alumni
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
     * @var string
     *
     * @ORM\Column(type="string", length="100", nullable=true)
     */
    private $juniorRemark;

     /**
      * @var integer
      *
      * @ORM\Column(type="smallint")
      */
    private $seniorSchool;

    /**
     * @var integer
     *
     * @ORM\Column(type="integer")
     */
    private $seniorRegistration;

    /**
     * @var integer
     *
     * @ORM\Column(type="smallint")
     */
    private $seniorClass;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length="100")
     */
    private $seniorRemark;

    /**
     * @var string
     *
     * @ORM\Column(type="json")
     */
    private $university;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    private $major;

    /**
     * @var string
     *
     * @ORM\Column(type="string",length=500)
     */
    private $workInfo;

    /**
     * @var string
     *
     * @ORM\Column(type="string",length=500)
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

    // add your own fields
}
