<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PreferenceRepository")
 */
class Preference
{
    /**
     * @var string
     *
     * @ORM\Id
     * @ORM\Column(type="string", length=128)
     */
    private $identifier;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=128)
     */
    private $remark;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=128)
     */
    private $type;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=4096)
     */
    private $content;

    /**
     * @return string
     */
    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    /**
     * @return string
     */
    public function getRemark(): string
    {
        return $this->remark;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
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

    const SCHOOL_PASTPAPER_HEADER = "school.pastpaper.header";
}
