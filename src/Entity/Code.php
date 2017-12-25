<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use DateTime;
use DateInterval;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CodeRepository")
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
     * @ORM\Column(type="datetimetz")
     */
    private $time;

    /**
     * @var DateTime
     *
     * @ORM\Column(type="datetimetz")
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
    // add your own fields

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


}
