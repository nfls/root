<?php

namespace App\Entity\School;

use App\Entity\User\User;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component;

/**
 * @ORM\Entity(repositoryClass="App\Repository\School\AlumniRepository")
 */
class Alumni
{
    /**
     * @var Uuid
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
     * @var \DateTime|null
     *
     * @ORM\Column(type="datetimetz", nullable=true)
     */
    private $submitTime;

    /**
     * @var integer
     *
     * @ORM\Column(type="smallint", nullable=true)
     */
    private $status = 0;

    const STATUS_NOT_SUBMITTED = 0;
    const STATUS_SUBMITTED = 1;
    const STATUS_CANCELED = 2;
    const STATUS_REVIEWING = 3;
    const STATUS_REJECTED = 4;
    const STATUS_PASSED = 5;
    const STATUS_EXPIRED = 6;

    /**
     * @var integer
     *
     * @ORM\Column(type="smallint", nullable=true)
     */
    private $userStatus;

    /**
     * @var string|null
     * @Assert\NotBlank(message="alumni.auth.error.chineseName")
     * @ORM\Column(type="string", nullable=true)
     */
    private $chineseName;

    /**
     * @var string|null
     * @Assert\NotBlank(message="alumni.auth.error.englishName")
     * @ORM\Column(type="string", nullable=true)
     */
    private $englishName;

    /**
     * @var \DateTime|null
     * @Assert\Date(message="alumni.auth.error.birthday")
     * @ORM\Column(type="date", nullable=true)
     */
    private $birthday;

    /**
     * @var integer|null
     * @Assert\Choice(choices={0,1,2},message="alumni.auth.error.gender.invalid")
     * @ORM\Column(type="smallint", nullable=true)
     */
    private $gender;

    /**
     * @var integer|null
     * @Assert\Choice(choices={0,1,2},message="alumni.auth.error.juniorSchool.invalid")
     * @ORM\Column(type="smallint", nullable=true)
     */
    private $juniorSchool;

    /**
     * @var integer|null
     * @Assert\GreaterThanOrEqual(value=1963,message="alumni.auth.error.juniorRegistration.lowerBound")
     * @ORM\Column(type="smallint", nullable=true)
     */
    private $juniorRegistration;

    /**
     * @var integer|null
     * @Assert\Range(min=1,
     *     max=12,
     *     minMessage="alumni.auth.error.juniorClass.lowerBound",
     *     maxMessage="alumni.auth.error.juniorClass.upperBound",
     *     invalidMessage="alumni.auth.error.juniorClass.invalid")
     * @ORM\Column(type="smallint", nullable=true)
     */
    private $juniorClass;

    /**
     * @var integer|null
     * @Assert\Choice(choices={0,1,2,3,4,5},message="alumni.auth.error.seniorSchool.invalid")
     * @ORM\Column(type="smallint", nullable=true)
     */
    private $seniorSchool;

    /**
     * @var integer|null
     * @Assert\GreaterThanOrEqual(value=1963,message="alumni.auth.error.juniorRegistration.lowerBound")
     * @ORM\Column(type="integer", nullable=true)
     */
    private $seniorRegistration;

    /**
     * @var integer|null
     * @Assert\Range(min=1,
     *     max=8,
     *     minMessage="alumni.auth.error.juniorClass.lowerBound",
     *     maxMessage="alumni.auth.error.juniorClass.upperBound",
     *     invalidMessage="alumni.auth.error.juniorClass.invalid")
     * @ORM\Column(type="smallint", nullable=true)
     */
    private $seniorClass;

    /**
     * @var string|null
     * @Assert\NotBlank(message="alumni.auth.error.university.blank")
     * @ORM\Column(type="string", nullable=true)
     */
    private $university;

    /**
     * @var string|null
     * @Assert\NotBlank(message="alumni.auth.error.major.blank")
     * @ORM\Column(type="string", nullable=true)
     */
    private $major;

    /**
     * @var string|null
     * @Assert\NotBlank(message="alumni.auth.error.workInfo.blank")
     * @ORM\Column(type="string",length=500, nullable=true)
     */
    private $workInfo;

    /**
     * @var string|null
     *  @Assert\NotBlank(message="alumni.auth.error.personalInfo.blank")
     * @ORM\Column(type="string",length=500, nullable=true)
     */
    private $personalInfo;

    /**
     * @var string|null
     * @Assert\NotBlank(message="alumni.auth.error.onlineContact.blank")
     * @ORM\Column(type="string",length=100, nullable=true)
     */
    private $onlineContact;

    /**
     * @var string|null
     * //TODO country
     * @ORM\Column(type="string",length=2, nullable=true)
     */

    private $country;

    /**
     * @var string|null
     * @Assert\NotBlank(message="alumni.auth.error.location.blank")
     * @ORM\Column(type="string",length=256, nullable=true)
     */
    private $location;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=300, nullable=true)
     */
    private $remark;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(type="date", nullable=true)
     */
    private $expireAt;

    /**
     * @return Uuid
     */
    public function getId(): Uuid
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
     * @return \DateTime|null
     */
    public function getSubmitTime(): ?\DateTime
    {
        return $this->submitTime;
    }

    /**
     * @return int
     */
    public function getStatus(): int
    {
        if($this->expireAt && $this->expireAt < new \DateTime())
            return self::STATUS_EXPIRED;
        return $this->status;
    }

    /**
     * @param int $status
     */
    public function setStatus(int $status): void
    {
        $this->status = $status;
    }

    /**
     * @return null|string
     */
    public function getChineseName(): ?string
    {
        return $this->chineseName;
    }

    /**
     * @param null|string $chineseName
     */
    public function setChineseName(?string $chineseName): void
    {
        $this->chineseName = $chineseName;
    }

    /**
     * @return null|string
     */
    public function getEnglishName(): ?string
    {
        return $this->englishName;
    }

    /**
     * @param null|string $englishName
     */
    public function setEnglishName(?string $englishName): void
    {
        $this->englishName = $englishName;
    }

    /**
     * @return \DateTime|null
     */
    public function getBirthday(): ?\DateTime
    {
        return $this->birthday;
    }

    /**
     * @param \DateTime|null $birthday
     */
    public function setBirthday(?\DateTime $birthday): void
    {
        $this->birthday = $birthday;
    }

    /**
     * @return int|null
     */
    public function getGender(): ?int
    {
        return $this->gender;
    }

    /**
     * @param int|null $gender
     */
    public function setGender(?int $gender): void
    {
        $this->gender = $gender;
    }

    /**
     * @return int|null
     */
    public function getJuniorSchool(): ?int
    {
        return $this->juniorSchool;
    }

    /**
     * @param int|null $juniorSchool
     */
    public function setJuniorSchool(?int $juniorSchool): void
    {
        $this->juniorSchool = $juniorSchool;
    }

    /**
     * @return int|null
     */
    public function getJuniorRegistration(): ?int
    {
        return $this->juniorRegistration;
    }

    /**
     * @param int|null $juniorRegistration
     */
    public function setJuniorRegistration(?int $juniorRegistration): void
    {
        $this->juniorRegistration = $juniorRegistration;
    }

    /**
     * @return int|null
     */
    public function getJuniorClass(): ?int
    {
        return $this->juniorClass;
    }

    /**
     * @param int|null $juniorClass
     */
    public function setJuniorClass(?int $juniorClass): void
    {
        $this->juniorClass = $juniorClass;
    }

    /**
     * @return int|null
     */
    public function getSeniorSchool(): ?int
    {
        return $this->seniorSchool;
    }

    /**
     * @param int|null $seniorSchool
     */
    public function setSeniorSchool(?int $seniorSchool): void
    {
        $this->seniorSchool = $seniorSchool;
    }

    /**
     * @return int|null
     */
    public function getSeniorRegistration(): ?int
    {
        return $this->seniorRegistration;
    }

    /**
     * @param int|null $seniorRegistration
     */
    public function setSeniorRegistration(?int $seniorRegistration): void
    {
        $this->seniorRegistration = $seniorRegistration;
    }

    /**
     * @return int|null
     */
    public function getSeniorClass(): ?int
    {
        return $this->seniorClass;
    }

    /**
     * @param int|null $seniorClass
     */
    public function setSeniorClass(?int $seniorClass): void
    {
        $this->seniorClass = $seniorClass;
    }

    /**
     * @return null|string
     */
    public function getUniversity(): ?string
    {
        return $this->university;
    }

    /**
     * @param null|string $university
     */
    public function setUniversity(?string $university): void
    {
        $this->university = $university;
    }

    /**
     * @return null|string
     */
    public function getMajor(): ?string
    {
        return $this->major;
    }

    /**
     * @param null|string $major
     */
    public function setMajor(?string $major): void
    {
        $this->major = $major;
    }

    /**
     * @return null|string
     */
    public function getWorkInfo(): ?string
    {
        return $this->workInfo;
    }

    /**
     * @param null|string $workInfo
     */
    public function setWorkInfo(?string $workInfo): void
    {
        $this->workInfo = $workInfo;
    }

    /**
     * @return null|string
     */
    public function getPersonalInfo(): ?string
    {
        return $this->personalInfo;
    }

    /**
     * @param null|string $personalInfo
     */
    public function setPersonalInfo(?string $personalInfo): void
    {
        $this->personalInfo = $personalInfo;
    }

    /**
     * @return null|string
     */
    public function getOnlineContact(): ?string
    {
        return $this->onlineContact;
    }

    /**
     * @param null|string $onlineContact
     */
    public function setOnlineContact(?string $onlineContact): void
    {
        $this->onlineContact = $onlineContact;
    }

    /**
     * @return null|string
     */
    public function getCountry(): ?string
    {
        return $this->country;
    }

    /**
     * @param null|string $country
     */
    public function setCountry(?string $country): void
    {
        $this->country = $country;
    }

    /**
     * @return null|string
     */
    public function getLocation(): ?string
    {
        return $this->location;
    }

    /**
     * @param null|string $location
     */
    public function setLocation(?string $location): void
    {
        $this->location = $location;
    }

    /**
     * @return null|string
     */
    public function getRemark(): ?string
    {
        return $this->remark;
    }

    /**
     * @param null|string $remark
     */
    public function setRemark(?string $remark): void
    {
        $this->remark = $remark;
    }

    /**
     * @return null|int
     */
    public function getUserStatus(): ?int
    {
        return $this->userStatus;
    }

    /**
     * @param int|null $userStatus
     */
    public function setUserStatus(?int $userStatus): void
    {
        $this->userStatus = $userStatus;
    }

    /**
     * @param \DateTime|null $submitTime
     */
    public function setSubmitTime(?\DateTime $submitTime): void
    {
        $this->submitTime = $submitTime;
    }

    /**
     * @return \DateTime|null
     */
    public function getExpireAt(): ?\DateTime
    {
        return $this->expireAt;
    }

    /**
     * @param \DateTime|null $expireAt
     */
    public function setExpireAt(?\DateTime $expireAt): void
    {
        $this->expireAt = $expireAt;
    }


    /**
     * @Assert\IsTrue(message="alumni.auth.error.notInNfls")
     */
    public function isAtNflsOnce(){
        if($this->userStatus == 4)
            return true;
        if($this->juniorSchool > 0)
            return true;
        if(null != $this->seniorSchool && $this->seniorSchool > 0)
            return true;
        else
            return false;
    }


}