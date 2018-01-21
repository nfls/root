<?php

namespace App\Entity\User;

use Doctrine\ORM\Mapping as ORM;
use League\OAuth2\Server\Entities\UserEntityInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * @ORM\Entity(repositoryClass="App\Repository\User\UserRepository")
 */
class User implements UserInterface,UserEntityInterface
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
     * @ORM\Column(type="string", unique=true)
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
     * @ORM\Column(type="bigint", unique=true)
     */
    private $phone;

    /**
     * @var integer
     *
     * @ORM\Column(type="string", unique=true)
     */
    private $token;

    /**
     * @var string
     *
     * @ORM\Column(type="string", unique=true, nullable = true)
     */
    private $fa;

    /**
     * @var integer
     *
     * @ORM\Column(type="integer", options={"unsigned":true, "default":0})
     */
    private $point = 0;


    /**
     * @var integer
     *
     * @ORM\Column(type="bigint", options={"unsigned":true, "default":1})
     */
    private $permission = 1;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetimetz", options={"default":"CURRENT_TIMESTAMP"})
     */
    private $readTime;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetimetz", options={"default":"CURRENT_TIMESTAMP"})
     */
    private $joinTime;

    public function __construct()
    {
        $this->joinTime = new \DateTime();
        $this->readTime = new \DateTime();
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
            $phoneObject = $util->parse($this->phone);
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
            "admin" => false
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

    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }

    public function getRoles()
    {
        return ["ROLES_USER"];
    }

    public function getIdentifier()
    {
        return $this;
    }


}

