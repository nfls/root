<?php

namespace App\Entity\School;

use App\Entity\User\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;

/**
 * @ORM\Entity(repositoryClass="App\Repository\School\ClazRepository")
 */
class Claz
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
    private $teacher;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="App\Entity\User\User",mappedBy="classes")
     * @ORM\JoinTable(name="user")
     */
    private $students;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="App\Entity\School\Notice",mappedBy="claz")
     */
    private $notices;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    private $announcement;

    /**
     * Claz constructor.
     */

    public function __construct()
    {
        //$this->teachers = new ArrayCollection();
        $this->students = new ArrayCollection();
        $this->notices = new ArrayCollection();
        $this->announcement = "";
    }

    /**
     * @return Uuid
     */
    public function getId(): Uuid
    {
        return $this->id;
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
    public function getAnnouncement(): string
    {
        return $this->announcement;
    }

    /**
     * @param string $announcement
     */
    public function setAnnouncement(string $announcement): void
    {
        $this->announcement = $announcement;
    }

    /**
     * @param User $student
     */
    public function addStudent(User $student): void
    {
        if(!$this->students->contains($student)){
            $this->students->add($student);
            $student->addClass($this);
        }

    }

    /**
     * @param User $student
     */
    public function removeStudent(User $student): void
    {
        if($this->students->contains($student)){
            $this->students->remove($student);
            $student->removeClass($this);
        }

    }

    /**
     * @param User $teacher
     */
    public function setTeacher(User $teacher): void
    {
        if(null !== $this->teacher)
            $this->removeStudent($this->teacher);
        $this->teacher = $teacher;
        $this->addStudent($teacher);
    }


    /**
     * @return User
     */
    public function getTeachers()
    {
        return $this->teacher;
    }

    /**
     * @return ArrayCollection
     */
    public function getStudents()
    {
        return $this->students;
    }

    /**
     * @return ArrayCollection
     */
    public function getNotices()
    {
        return $this->notices;
    }

}
