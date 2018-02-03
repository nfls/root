<?php

namespace App\Entity\User;

use Doctrine\ORM\Mapping as ORM;
use DateTime;
use DateInterval;

/**
 * @ORM\Entity(repositoryClass="App\Repository\User\CodeRepository")
 */
class Code
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var DateTime
     *
     * @ORM\Column(type="datetime")
     */
    private $time;

    /**
     * @var DateTime
     *
     * @ORM\Column(type="datetime")
     */
    private $expired;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=1024)
     */
    private $destination;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    private $code;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    private $action;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    private $type;

    public function __construct()
    {
        $this->time = new DateTime();
        $this->expired = new DateTime();
        $this->expired->add(new DateInterval('PT5M'));
    }

    /**
     * @param mixed $destination
     */
    public function setDestination($destination)
    {
        $this->destination = $destination;
    }

    /**
     * @param mixed $code
     */
    public function setCode($code)
    {
        $this->code = $code;
    }

    /**
     * @param mixed $action
     */
    public function setAction($action)
    {
        $this->action = $action;
    }

    /**
     * @param mixed $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getCode(): string
    {
        return $this->code;
    }



}
