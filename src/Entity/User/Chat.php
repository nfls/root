<?php

namespace App\Entity\User;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;

/**
 * @ORM\Entity(repositoryClass="App\Repository\User\ChatRepository")
 */
class Chat
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
     * @ORM\ManyToOne(targetEntity="App\Entity\User\User")
     */
    private $sender;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\User\User")
     */
    private $receiver;

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
     * @var boolean
     */
    public $canReply = false;

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
     * @return User
     */
    public function getSender(): User
    {
        return $this->sender;
    }

    /**
     * @param User $sender
     */
    public function setSender(User $sender): void
    {
        $this->sender = $sender;
    }

    /**
     * @return User
     */
    public function getReceiver(): User
    {
        return $this->receiver;
    }

    /**
     * @param User $receiver
     */
    public function setReceiver(User $receiver): void
    {
        $this->receiver = $receiver;
    }

    /**
     * @return \DateTime
     */
    public function getTime(): \DateTime
    {
        return $this->time;
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

}
