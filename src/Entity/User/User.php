<?php

namespace App\Entity\User;

use App\Entity\School\Alumni;
use App\Entity\Media\Gallery;
use App\Entity\Media\Photo;
use App\Entity\School\Claz;
use App\Model\Permission;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use League\OAuth2\Server\Entities\UserEntityInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * @ORM\Entity(repositoryClass="App\Repository\User\UserRepository")
 */
class User implements UserInterface,UserEntityInterface,\JsonSerializable
{

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer", unique=true)
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string", unique=true)
     */
    private $username;

    /**
     * @var string
     *
     * @ORM\Column(type="string", unique=true, nullable = true)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @var integer
     *
     * @ORM\Column(type="bigint", unique=true, nullable = true)
     */
    private $phone;

    /**
     * @var integer
     *
     * @ORM\Column(type="string", unique=true)
     */
    private $token;

    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean")
     */
    private $admin = false;

    /**
     * @var integer
     *
     * @ORM\Column(type="integer", options={"unsigned":true, "default":0})
     */
    private $point = 0;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", options={"default":"CURRENT_TIMESTAMP"})
     */
    private $readTime;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", options={"default":"CURRENT_TIMESTAMP"})
     */
    private $joinTime;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="App\Entity\School\Alumni", mappedBy="user")
     */
    private $authTickets;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="App\Entity\School\Claz", inversedBy="students")
     */
    private $classes;

    /**
     * @var bool
     */
    public $isOAuth = false;

    /**
     * @var string
     */
    public $realUsername = null;

    /**
     * @var string
     */
    public $htmlUsername = null;

    public function __construct()
    {
        $this->joinTime = new \DateTime();
        $this->readTime = new \DateTime();
        $this->authTickets = new ArrayCollection();
        $this->classes = new ArrayCollection();
    }
    
    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param string $username
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }


    /**
     * @return \libphonenumber\PhoneNumber
     */
    public function getPhone()
    {
        $util = \libphonenumber\PhoneNumberUtil::getInstance();
        try {
            $phoneObject = $util->parse("+" . $this->phone);
            return $phoneObject;
        }catch(\libphonenumber\NumberParseException $e){
            return null;
        }
    }

    /**
     * @param int $phone
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;
    }

    /**
     * @return int
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
        $this->token = uniqid("nfls_",true);
    }

    /**
     * @return \DateTime
     */
    public function getReadTime()
    {
        return $this->readTime;
    }

    /**
     * @param \DateTime $readTime
     */
    public function setReadTime($readTime)
    {
        $this->readTime = $readTime;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    public function getInfoArray()
    {
        return array(
            "id" => $this->id,
            "point" => $this->point,
            "username" => $this->username,
            "email" => $this->email,
            "phone" => $this->phone,
            "joinTime" => $this->joinTime,
            "admin" => $this->isAdmin(),
            "verified" => $this->isVerified()
        );
    }

    /**
     * @return int
     */
    public function getPoint()
    {
        return $this->point;
    }

    /**
     * @param int $point
     */
    public function setPoint($point)
    {
        $this->point = $point;
    }

    /**
     * @param int $point
     */
    public function minusPoints($point){
        $this->point -= $point;
    }

    /**
     * @return \DateTime
     */
    public function getJoinTime()
    {
        return $this->joinTime;
    }

    public function getSalt()
    {
        return null;
    }

    public function eraseCredentials(){}

    public function getRoles()
    {
        if($this->isAdmin())
            $permissions = [Permission::IS_ADMIN];
        else
            $permissions = [];
        $valid = array_filter($this->authTickets->toArray(),array($this,"getValid"));
        if(count($valid) > 0){
            /** @var $auth Alumni*/
            $auth = array_values($valid)[0];
            $this->realUsername = $this->username . "( " . $auth->getEnglishName() . " | " . $auth->getChineseName() . " )";
            $this->htmlUsername = $this->username."&nbsp;<span style='background-color:#3CDBC0;'>".$auth->getEnglishName()."</span>&nbsp;<span style='background-color:#2DCCD3;'>". $auth->getChineseName() ."</span>";
            array_push($permissions,Permission::IS_AUTHENTICATED);
            array_push($permissions,$this->getAlumniPermission(array_values($valid)[0]));
        }
        return $permissions;
    }

    private function isVerified(){
        $valid = array_filter($this->authTickets->toArray(),array($this,"getValid"));
        if(count($valid) > 0)
            return true;
        else
            return false;
    }

    public function hasRole($role){
        $roles = $this->getRoles();
        if(in_array($role,$roles))
            return true;
        else
            return false;
    }

    /**
     * @param $val Alumni
     * @return bool
     */
    public function getValid($val){
        if($val->getStatus() == Alumni::STATUS_PASSED)
            return true;
        else
            return false;
    }

    /**
     * @param $val Alumni
     * @return bool
     */
    public function getAlumniPermission($val){
        switch($val->getUserStatus()){
            case 0:
            case 1:
                return Permission::IS_STUDENT;
            case 2:
            case 3:
                return Permission::IS_GRADUATE;
            case 4:
                return Permission::IS_TEACHER;
        }
    }

    /**
     * @return bool
     */
    public function isAdmin(): bool
    {
        return $this->admin;
    }


    public function getIdentifier()
    {
        return $this;
    }

    public function jsonSerialize()
    {
        return $this->getId();
    }

    /**
     * @param \DateTime $joinTime
     */
    public function setJoinTime(\DateTime $joinTime): void
    {
        $this->joinTime = $joinTime;
    }

    /**
     * @return ArrayCollection
     */
    public function getClasses()
    {
        return $this->classes;
    }

    public function addClass(Claz $class){
        if(!$this->classes->contains($class)){
            $this->classes->add($class);
        }
    }

    public function removeClass(Claz $class){
        if($this->classes->contains($class)){
            $this->classes->removeElement($class);
        }
    }

    public function __toString() {
        return $this->username;
    }
}

