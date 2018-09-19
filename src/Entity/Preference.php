<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PreferenceRepository")
 */
class Preference
{
    const SCHOOL_PASTPAPER_HEADER = "school.pastpaper.header";
    const DASHBOARD_ANNOUNCEMENT = "dashboard.announcement";
    const IOS_ANNOUNCEMENT = "app.ios.announcement";
    const ABOUT_DEVS = "about.devs";
    const ALUMNI_HEADER = "alumni.auth.header";
    const PHOTOS = "index.photos";
    //const ALL = [self::SCHOOL_PASTPAPER_HEADER, self::ABOUT_DEVS];
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
}
