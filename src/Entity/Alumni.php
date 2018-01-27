<?php

namespace App\Entity;

use App\Entity\User\User;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidType;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AlumniRepository")
 */
class Alumni {
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
     * @ORM\Column(type="datetimetz", nullable=true)
     */
    private $submitTime;

    /**
     * @var boolean
     *
     * @ORM\Column(type="smallint", nullable=true)
     */
    private $status;

    const STATUS_NOT_SUBMITTED = 0;
    const STATUS_SUBMITTED = 1;
    const STATUS_REVIEWING = 2;
    const STATUS_REJECTED = 3;
    const STATUS_PASSED = 4;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $chineseName;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $englishName;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="date", nullable=true)
     */
    private $birthday;

    /**
     * @var integer
     *
     * @ORM\Column(type="smallint", nullable=true)
     */
    private $gender;

    /**
     * @var integer
     *
     * @ORM\Column(type="smallint", nullable=true)
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
      * @ORM\Column(type="smallint", nullable=true)
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
     * @ORM\Column(type="string",length=100, nullable=true)
     */
    private $onlineContact;

    /**
     * @var string
     *
     * @ORM\Column(type="json",length=256, nullable=true)
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

    /**
     * @return bool
     */
    public function isStatus()
    {
        return $this->status;
    }

    /**
     * @param bool $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @return string
     */
    public function getChineseName()
    {
        return $this->chineseName;
    }

    /**
     * @param string $chineseName
     */
    public function setChineseName($chineseName)
    {
        $this->chineseName = $chineseName;
    }

    /**
     * @return string
     */
    public function getEnglishName()
    {
        return $this->englishName;
    }

    /**
     * @param string $englishName
     */
    public function setEnglishName($englishName)
    {
        $this->englishName = $englishName;
    }

    /**
     * @return \DateTime
     */
    public function getBirthday()
    {
        return $this->birthday;
    }

    /**
     * @param \DateTime $birthday
     */
    public function setBirthday($birthday)
    {
        $this->birthday = $birthday;
    }

    /**
     * @return int
     */
    public function getGender()
    {
        return $this->gender;
    }

    /**
     * @param int $gender
     */
    public function setGender($gender)
    {
        $this->gender = $gender;
    }

    /**
     * @return int
     */
    public function getJuniorSchool()
    {
        return $this->juniorSchool;
    }

    /**
     * @param int $juniorSchool
     */
    public function setJuniorSchool($juniorSchool)
    {
        $this->juniorSchool = $juniorSchool;
    }

    /**
     * @return int
     */
    public function getJuniorRegistration()
    {
        return $this->juniorRegistration;
    }

    /**
     * @param int $juniorRegistration
     */
    public function setJuniorRegistration($juniorRegistration)
    {
        $this->juniorRegistration = $juniorRegistration;
    }

    /**
     * @return int
     */
    public function getJuniorClass()
    {
        return $this->juniorClass;
    }

    /**
     * @param int $juniorClass
     */
    public function setJuniorClass($juniorClass)
    {
        $this->juniorClass = $juniorClass;
    }

    /**
     * @return int
     */
    public function getSeniorSchool()
    {
        return $this->seniorSchool;
    }

    /**
     * @param int $seniorSchool
     */
    public function setSeniorSchool($seniorSchool)
    {
        $this->seniorSchool = $seniorSchool;
    }

    /**
     * @return int
     */
    public function getSeniorRegistration()
    {
        return $this->seniorRegistration;
    }

    /**
     * @param int $seniorRegistration
     */
    public function setSeniorRegistration($seniorRegistration)
    {
        $this->seniorRegistration = $seniorRegistration;
    }

    /**
     * @return int
     */
    public function getSeniorClass()
    {
        return $this->seniorClass;
    }

    /**
     * @param int $seniorClass
     */
    public function setSeniorClass($seniorClass)
    {
        $this->seniorClass = $seniorClass;
    }

    /**
     * @return string
     */
    public function getUniversity()
    {
        return $this->university;
    }

    /**
     * @param string $university
     */
    public function setUniversity($university)
    {
        $this->university = $university;
    }

    /**
     * @return string
     */
    public function getMajor()
    {
        return $this->major;
    }

    /**
     * @param string $major
     */
    public function setMajor($major)
    {
        $this->major = $major;
    }

    /**
     * @return string
     */
    public function getWorkInfo()
    {
        return $this->workInfo;
    }

    /**
     * @param string $workInfo
     */
    public function setWorkInfo($workInfo)
    {
        $this->workInfo = $workInfo;
    }

    /**
     * @return string
     */
    public function getPersonalInfo()
    {
        return $this->personalInfo;
    }

    /**
     * @param string $personalInfo
     */
    public function setPersonalInfo($personalInfo)
    {
        $this->personalInfo = $personalInfo;
    }

    /**
     * @return string
     */
    public function getOnlineContact()
    {
        return $this->onlineContact;
    }

    /**
     * @param string $onlineContact
     */
    public function setOnlineContact($onlineContact)
    {
        $this->onlineContact = $onlineContact;
    }

    /**
     * @return string
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * @param string $location
     */
    public function setLocation($location)
    {
        $this->location = $location;
    }

    /**
     * @return string
     */
    public function getRemark()
    {
        return $this->remark;
    }

    /**
     * @param string $remark
     */
    public function setRemark($remark)
    {
        $this->remark = $remark;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param User $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }


}
