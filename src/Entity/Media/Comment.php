<?php

namespace App\Entity\Media;

use App\Entity\User\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Media\CommentRepository")
 */
class Comment
{
    /**
     * @var integer
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=150)
     */
    private $content;

    /**
     * @var Gallery
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Media\Gallery", inversedBy="comments")
     */
    private $gallery;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\User\User")
     */
    private $postUser;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetimetz")
     */
    private $time;



    public function __construct()
    {
        $time = new \DateTime();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @return User
     */
    public function getPostUser()
    {
        return $this->postUser;
    }

    /**
     * @return \DateTime
     */
    public function getTime()
    {
        return $this->time;
    }


    // add your own fields
}
