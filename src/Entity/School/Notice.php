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
    private $title;

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

    /**
     * @return Uuid
     */
    public function getId(): Uuid
    {
        return $this->id;
    }


    /**
     * @return Claz
     */
    public function getClaz(): Claz
    {
        return $this->claz;
    }

    /**
     * @param Claz $claz
     */
    public function setClaz(Claz $claz): void
    {
        $this->claz = $claz;
    }

    /**
     * @return \DateTime
     */
    public function getTime(): \DateTime
    {
        return $this->time;
    }

    /**
     * @param \DateTime $time
     */
    public function setTime(\DateTime $time): void
    {
        $this->time = $time;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * @param string $content
     */
    public function setContent(string $content): void
    {
        $this->content = $content;
    }

    /**
     * @return array
     */
    public function getAttachment(): array
    {
        return $this->attachment;
    }

    /**
     * @param array $attachment
     */
    public function setAttachment(array $attachment): void
    {
        $this->attachment = $attachment;
    }

    /**
     * @return \DateTime
     */
    public function getDeadline(): \DateTime
    {
        return $this->deadline;
    }

    /**
     * @param \DateTime $deadline
     */
    public function setDeadline(\DateTime $deadline): void
    {
        $this->deadline = $deadline;
    }



}
