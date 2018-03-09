<?php

namespace App\Entity\Media;

use App\Entity\User\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Media\GalleryRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Gallery
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
     * @ORM\Column(type="string", length=150)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=1024)
     */
    private $description;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Media\Photo", mappedBy="gallery")
     */
    private $photos;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Media\Comment", mappedBy="gallery")
     * @ORM\OrderBy({"time" = "DESC"})
     */
    private $comments;

    /**
     * @var array
     *
     * @ORM\Column(type="json")
     */
    private $likes;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", options={"default":"CURRENT_TIMESTAMP"})
     */
    private $time;

    /**
     * @var Photo
     *
     * @ORM\OneToOne(targetEntity="App\Entity\Media\Photo")
     */
    private $cover;

    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean")
     */
    private $isVisible = false;

    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean")
     */
    private $isPublic = false;

    /**
     * @var integer
     *
     * @ORM\Column(type="smallint", options={"default":1})
     */
    private $priority = 1;

    /**
     * @var EntityManager;
     */
    private $em;

    public function __construct()
    {
        $this->photos = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->time = new \DateTime();
        $this->likes = [];
    }

    /**
     * @ORM\PostLoad
     * @ORM\PostPersist
     */
    public function fetchEntityManager(LifecycleEventArgs $args)
    {
        $this->em = $args->getEntityManager();
    }

    /**
     * @param ArrayCollection $photo
     */
    public function addPhotos($photo)
    {
        $this->photos->add($photo);
    }

    /**
     * @param ArrayCollection $comment
     */
    public function addComments($comment)
    {
        $this->comments->add($comment);
    }

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
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return integer
     */
    public function getPhotoCount()
    {
        return count($this->photos);
    }

    /**
     * @return integer
     */
    public function getOriginCount()
    {
        return count($this->photos->filter(function ($val) {
            return !(null === $val->getOrigin());
        }));
    }

    /**
     * @return ArrayCollection
     */
    public function getPhotos()
    {
        return $this->photos;
    }

    /**
     * @param ArrayCollection $photos
     */
    public function setPhotos(ArrayCollection $photos): void
    {
        $this->photos = $photos;
    }

    /**
     * @return ArrayCollection
     */
    public function getComments()
    {
        return $this->comments;
    }

    /**
     * @return \DateTime
     */
    public function getTime()
    {
        return $this->time;
    }

    /**
     * @return bool
     */
    public function isVisible()
    {
        return $this->isVisible;
    }

    /**
     * @param bool $isVisible
     */
    public function setIsVisible($isVisible)
    {
        $this->isVisible = $isVisible;
    }

    /**
     * @return bool
     */
    public function isPublic()
    {
        return $this->isPublic;
    }

    /**
     * @param bool $isPublic
     */
    public function setIsPublic($isPublic)
    {
        $this->isPublic = $isPublic;
    }

    /**
     * @return Photo
     */
    public function getCover()
    {
        return $this->cover;
    }

    /**
     * @param Photo $cover
     */
    public function setCover($cover)
    {
        $this->cover = $cover;
    }

    /**
     * @return int
     */
    public function getPriority(): int
    {
        return $this->priority;
    }

    /**
     * @param int $priority
     */
    public function setPriority(int $priority): void
    {
        $this->priority = $priority;
    }

    /**
     * @return array
     */
    public function getLikes()
    {
        if(is_array($this->likes)){
            $repo = $this->em->getRepository(User::class);
            return array_map(function ($id) use ($repo) {
                return $repo->findOneBy(["id" => $id]);
            }, $this->likes);
        } else {
            return [];
        }

    }

    /**
     * @param User $user
     *
     * @return boolean
     */
    public function likeStatus($user)
    {
        $likes = $this->getLikes();
        if (null === $user) {
            $status = false;
        } else if (in_array($user, $likes)) {
            $status = true;
        } else {
            $status = false;
        }
        return $status;
    }

    /**
     * @param User $user
     */
    public function like($user)
    {
        if (($key = array_search($user->getId(), $this->likes)) !== false) {
            unset($this->likes[$key]);
        } else {
            array_push($this->likes, $user->getId());
        }
    }

    public function removeAllComments()
    {
        $this->comments = new ArrayCollection();
    }

    public function removeAllPhotos()
    {
        $this->photos = new ArrayCollection();
    }

    public function removePhoto($photo)
    {
        $this->photos->removeElement($photo);
        if($this->cover === $photo)
            $this->cover = null;
    }

}
