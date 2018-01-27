<?php

namespace App\Entity;

use App\Entity\User\User;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidType;
use Symfony\Component\Validator\Constraints as Assert;

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
    const STATUS_CANCELED = 2;
    const STATUS_REVIEWING = 3;
    const STATUS_REJECTED = 4;
    const STATUS_PASSED = 5;

    /**
     * @var string
     * @Assert\NotBlank(message="alumni.error.incomplete")
     * @ORM\Column(type="string", nullable=true)
     */
    private $chineseName;

    /**
     * @var string
     * @Assert\NotBlank(message="alumni.error.incomplete")
     * @ORM\Column(type="string", nullable=true)
     */
    private $englishName;

    /**
     * @var \DateTime
     * @Assert\NotBlank(message="alumni.error.incomplete")
     * @ORM\Column(type="date", nullable=true)
     */
    private $birthday;

    /**
     * @var integer
     * @Assert\NotBlank(message="alumni.error.incomplete")
     * @ORM\Column(type="smallint", nullable=true)
     */
    private $gender;

    /**
     * @var integer
     * @Assert\NotBlank(message="alumni.error.incomplete")
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
     * @Assert\NotBlank(message="alumni.error.incomplete")
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
     * @ORM\Column(type="string",length=2, nullable=true)
     */

    private $country;

    /**
     * @var string
     *
     * @ORM\Column(type="string",length=256, nullable=true)
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
     * @return UuidType
     */
    public function getId(): UuidType
    {
        return $this->id;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @param User $user
     */
    public function setUser(User $user): void
    {
        $this->user = $user;
    }

    /**
     * @return \DateTime
     */
    public function getSubmitTime(): \DateTime
    {
        return $this->submitTime;
    }

    /**
     * @return bool
     */
    public function isStatus(): bool
    {
        return $this->status;
    }

    /**
     * @param bool $status
     */
    public function setStatus(bool $status): void
    {
        $this->status = $status;
    }

    /**
     * @return string
     */
    public function getChineseName(): string
    {
        return $this->chineseName;
    }

    /**
     * @param string $chineseName
     */
    public function setChineseName(string $chineseName): void
    {
        $this->chineseName = $chineseName;
    }

    /**
     * @return string
     */
    public function getEnglishName(): string
    {
        return $this->englishName;
    }

    /**
     * @param string $englishName
     */
    public function setEnglishName(string $englishName): void
    {
        $this->englishName = $englishName;
    }

    /**
     * @return \DateTime
     */
    public function getBirthday(): \DateTime
    {
        return $this->birthday;
    }

    /**
     * @param \DateTime $birthday
     */
    public function setBirthday(\DateTime $birthday): void
    {
        $this->birthday = $birthday;
    }

    /**
     * @return int
     */
    public function getGender(): int
    {
        return $this->gender;
    }

    /**
     * @param int $gender
     */
    public function setGender(int $gender): void
    {
        $this->gender = $gender;
    }

    /**
     * @return int
     */
    public function getJuniorSchool(): int
    {
        return $this->juniorSchool;
    }

    /**
     * @param int $juniorSchool
     */
    public function setJuniorSchool(int $juniorSchool): void
    {
        $this->juniorSchool = $juniorSchool;
    }

    /**
     * @return int
     */
    public function getJuniorRegistration(): int
    {
        return $this->juniorRegistration;
    }

    /**
     * @param int $juniorRegistration
     */
    public function setJuniorRegistration(int $juniorRegistration): void
    {
        $this->juniorRegistration = $juniorRegistration;
    }

    /**
     * @return int
     */
    public function getJuniorClass(): int
    {
        return $this->juniorClass;
    }

    /**
     * @param int $juniorClass
     */
    public function setJuniorClass(int $juniorClass): void
    {
        $this->juniorClass = $juniorClass;
    }

    /**
     * @return int
     */
    public function getSeniorSchool(): int
    {
        return $this->seniorSchool;
    }

    /**
     * @param int $seniorSchool
     */
    public function setSeniorSchool(int $seniorSchool): void
    {
        $this->seniorSchool = $seniorSchool;
    }

    /**
     * @return int
     */
    public function getSeniorRegistration(): int
    {
        return $this->seniorRegistration;
    }

    /**
     * @param int $seniorRegistration
     */
    public function setSeniorRegistration(int $seniorRegistration): void
    {
        $this->seniorRegistration = $seniorRegistration;
    }

    /**
     * @return int
     */
    public function getSeniorClass(): int
    {
        return $this->seniorClass;
    }

    /**
     * @param int $seniorClass
     */
    public function setSeniorClass(int $seniorClass): void
    {
        $this->seniorClass = $seniorClass;
    }

    /**
     * @return string
     */
    public function getUniversity(): string
    {
        return $this->university;
    }

    /**
     * @param string $university
     */
    public function setUniversity(string $university): void
    {
        $this->university = $university;
    }

    /**
     * @return string
     */
    public function getMajor(): string
    {
        return $this->major;
    }

    /**
     * @param string $major
     */
    public function setMajor(string $major): void
    {
        $this->major = $major;
    }

    /**
     * @return string
     */
    public function getWorkInfo(): string
    {
        return $this->workInfo;
    }

    /**
     * @param string $workInfo
     */
    public function setWorkInfo(string $workInfo): void
    {
        $this->workInfo = $workInfo;
    }

    /**
     * @return string
     */
    public function getPersonalInfo(): string
    {
        return $this->personalInfo;
    }

    /**
     * @param string $personalInfo
     */
    public function setPersonalInfo(string $personalInfo): void
    {
        $this->personalInfo = $personalInfo;
    }

    /**
     * @return string
     */
    public function getOnlineContact(): string
    {
        return $this->onlineContact;
    }

    /**
     * @param string $onlineContact
     */
    public function setOnlineContact(string $onlineContact): void
    {
        $this->onlineContact = $onlineContact;
    }

    /**
     * @return string
     */
    public function getCountry(): string
    {
        return $this->country;
    }

    /**
     * @param string $country
     */
    public function setCountry(string $country): void
    {
        $this->country = $country;
    }

    /**
     * @return string
     */
    public function getLocation(): string
    {
        return $this->location;
    }

    /**
     * @param string $location
     */
    public function setLocation(string $location): void
    {
        $this->location = $location;
    }

    /**
     * @return string
     */
    public function getRemark(): string
    {
        return $this->remark;
    }

    /**
     * @param string $remark
     */
    public function setRemark(string $remark): void
    {
        $this->remark = $remark;
    }

}