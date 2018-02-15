<?php

namespace App\Entity\School;

use App\Entity\User\User;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;

/**
 * @ORM\Entity(repositoryClass="App\Repository\School\NoticeRepository")
 */
class Notice
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
     * @var Claz
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\School\Claz",inversedBy="notices")
     */
    private $claz;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetimetz")
     */
    private $time;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=1024)
     */
    private $content;

    /**
     * @var array
     *
     * @ORM\Column(type="json")
     */
    private $attachment;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetimetz", nullable=true)
     */
    private $deadline;

    /**
     * Notice constructor.
     */

    public function __construct()
    {
        $this->time = new \DateTime();
    }
}
