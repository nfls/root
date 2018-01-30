<?php

namespace App\Entity\Game;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Game\GameRepository")
 */
class Game
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=1024)
     */
    private $thumb;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=128)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=128)
     */
    private $subTitle;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=1024)
     */
    private $description;

    /**
     * @var array
     *
     * @ORM\Column(type="json", length=1024)
     */
    private $content;

    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean")
     */
    private $preferBigger;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getThumb(): string
    {
        return $this->thumb;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @return string
     */
    public function getSubTitle(): string
    {
        return $this->subTitle;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @return array
     */
    public function getContent(): array
    {
        return $this->content;
    }

    /**
     * @return bool
     */
    public function isPreferBigger(): bool
    {
        return $this->preferBigger;
    }



}
