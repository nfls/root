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
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Media\Comment", mappedBy="replyTo")
     */
    private $replies;

    /**
     * @var Comment
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Media\Comment", inversedBy="replies")
     */
    private $replyTo;

    public function __construct()
    {
        $this->replies = new ArrayCollection();
    }
    // add your own fields
}
