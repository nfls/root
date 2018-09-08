<?php

namespace App\Entity\School;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;

/**
 * @ORM\Entity(repositoryClass="App\Repository\School\VoteRepository")
 */
class Vote implements \JsonSerializable
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
     * @var string
     *
     * @ORM\Column(type="text")
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(type="text")
     */
    private $content;

    /**
     * @var array
     *
     * @ORM\Column(type="json")
     */
    private $options = [];

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime")
     */
    private $time;
    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean")
     */
    private $enabled = false;

    public function __construct()
    {
        $this->time = new \DateTime();
    }

    public function getId()
    {
        return $this->id ?? "new";
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title ?? "";
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
        return $this->content ?? "";
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
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * @param array $options
     */
    public function setOptions(array $options): void
    {
        $this->options = $options;
    }

    /**
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    /**
     * @param bool $enabled
     */
    public function setEnabled(bool $enabled): void
    {
        $this->enabled = $enabled;
    }

    public function jsonSerialize()
    {
        return [
            "id" => $this->getId(),
            "title" => $this->getTitle()
        ];
    }


}
