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
     * @var string
     *
     * @ORM\Column(type="json")
     */
    private $likes;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetimetz", options={"default":"CURRENT_TIMESTAMP"})
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
     * @var EntityManager;
     */
    private $em;

    /**
     * @ORM\PostLoad
     * @ORM\PostPersist
     */
    public function fetchEntityManager(LifecycleEventArgs $args)
    {
        $this->em = $args->getEntityManager();
    }

    public function __construct()
    {
        $this->photos = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->time = new \DateTime();
        $this->likes = json_encode([]);
    }

    /**
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
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
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @return integer
     */
    public function getPhotoCount(){
        return count($this->photos);
    }

    /**
     * @return integer
     */
    public function getOriginCount(){
        return count($this->photos->filter(function($val){
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
     * @return bool
     */
    public function isPublic()
    {
        return $this->isPublic;
    }



    /**
     * @param bool $isVisible
     */
    public function setIsVisible($isVisible)
    {
        $this->isVisible = $isVisible;
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
     * @return array
     */
    public function getLikes()
    {
        if(null === $this->likes){
            $this->likes = json_encode([]);
        }
        $repo = $this->em->getRepository(User::class);
        return array_map(function($id)use($repo){
            return $repo->findOneBy(["id"=>$id]);
        },json_decode($this->likes,true));
    }


    /**
     * @param User $user
     *
     * @return boolean
     */
    public function likeStatus($user){
        $repo = $this->em->getRepository(User::class);
        if(null === $this->likes){
            $this->likes = json_encode([]);
        }
        $likes = array_map(function($id)use($repo){
            return $repo->findOneBy(["id"=>$id]);
        },json_decode($this->likes,true));
        if(null === $user){
            $status = false;
        }else if(in_array($user,$likes)){
            $status = true;
        }else{
            $status = false;
        }
        return $status;
    }
    /**
     * @param User $user
     */
    public function like($user){
        if(null === $this->likes){
            $this->likes = json_encode([]);
        }
        $likes = json_decode($this->likes);
        if (($key = array_search($user->getId(), $likes)) !== false) {
            unset($likes[$key]);
        }else{
            array_push($likes,$user->getId());
        }
        $this->likes = json_encode($likes);
    }

    /**
     * @param Photo $cover
     */
    public function setCover($cover)
    {
        $this->cover = $cover;
    }

    public function removeAllComments(){
        $this->comments = new ArrayCollection();
    }

    /**
     * @param ArrayCollection $photos
     */
    public function setPhotos(ArrayCollection $photos): void
    {
        $this->photos = $photos;
    }



}
