<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CourseRepository")
 */
class Course
{
    const AVAILABLE = [
        0 => 'En construction',
        1 => 'Disponible',
        2 => 'Terminé'
    ];

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="datetime")
     */
    private $startAt;

    /**
     * @ORM\Column(type="datetime")
     */
    private $endAt;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $image;

    /**
     * @ORM\Column(type="integer")
     */
    private $status;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Category", inversedBy="course")
     * @ORM\JoinColumn(nullable=false)
     */
    private $category;


    /**
     * @ORM\OneToMany(targetEntity="App\Entity\CourseObject", mappedBy="course", orphanRemoval=true)
     */
    private $courseObjects;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Classroom", inversedBy="course")
     * @ORM\JoinColumn(nullable=false)
     */
    private $classrooms;

    /**
     * @ORM\Column(type="boolean")
     */
    private $is_uploadEnd;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\UserCourse", mappedBy="course", orphanRemoval=true)
     */
    private $userCourses;

    /**
     * @ORM\ManyToMany(targetEntity=Notifications::class, mappedBy="course")
     */
    private $notifications;

    public function __construct()
    {
        $this->courseObjects = new ArrayCollection();
        $this->userCourses = new ArrayCollection();
        $this->notifications = new ArrayCollection();
        $this->classrooms = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getStartAt(): ?\DateTimeInterface
    {
        return $this->startAt;
    }

    public function setStartAt(\DateTimeInterface $startAt): self
    {
        $this->startAt = $startAt;

        return $this;
    }

    public function getEndAt(): ?\DateTimeInterface
    {
        return $this->endAt;
    }

    public function setEndAt(\DateTimeInterface $endAt): self
    {
        $this->endAt = $endAt;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getStatus(): ?int
    {
        return $this->status;
    }

    public function setStatus(int $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getStatusType() 
    {
        return self::AVAILABLE[$this->status];
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }

    /**
     * @return Collection|CourseObject[]
     */
    public function getCourseObjects(): Collection
    {
        return $this->courseObjects;
    }

    public function addCourseObject(CourseObject $courseObject): self
    {
        if (!$this->courseObjects->contains($courseObject)) {
            $this->courseObjects[] = $courseObject;
            $courseObject->setCourse($this);
        }

        return $this;
    }

    public function removeCourseObject(CourseObject $courseObject): self
    {
        if ($this->courseObjects->contains($courseObject)) {
            $this->courseObjects->removeElement($courseObject);
            // set the owning side to null (unless already changed)
            if ($courseObject->getCourse() === $this) {
                $courseObject->setCourse(null);
            }
        }

        return $this;
    }

    /**
    * @return Collection|Classroom[]
    */
    public function getClassroom(): Collection
    {
        return $this->classrooms;
    }
    public function addClassroom(Classroom $class): self
    {
        if (!$this->classrooms->contains($class)) {
            $this->classrooms[] = $class;
            $class->addCourse($this);
        }
        return $this;
    }
    public function removeClassroom(Classroom $class): self
    {
        if ($this->classrooms->contains($class)) {
            $this->classrooms->removeElement($class);
            if ($class->getCourse() === $this) {
                $class->removeCourse($this);
            }
        }
        return $this;
    }

    public function getIsUploadEnd(): ?bool
    {
        return $this->is_uploadEnd;
    }

    public function setIsUploadEnd(bool $is_uploadEnd): self
    {
        $this->is_uploadEnd = $is_uploadEnd;

        return $this;
    }

    /**
     * @return Collection|UserCourse[]
     */
    public function getUserCourses(): Collection
    {
        return $this->userCourses;
    }

    public function addUserCourse(UserCourse $userCourse): self
    {
        if (!$this->userCourses->contains($userCourse)) {
            $this->userCourses[] = $userCourse;
            $userCourse->setCourse($this);
        }

        return $this;
    }

    public function removeUserCourse(UserCourse $userCourse): self
    {
        if ($this->userCourses->contains($userCourse)) {
            $this->userCourses->removeElement($userCourse);
            // set the owning side to null (unless already changed)
            if ($userCourse->getCourse() === $this) {
                $userCourse->setCourse(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Notifications[]
     */
    public function getNotifications(): Collection
    {
        return $this->notifications;
    }

    public function addNotification(Notifications $notification): self
    {
        if (!$this->notifications->contains($notification)) {
            $this->notifications[] = $notification;
            $notification->addCourse($this);
        }

        return $this;
    }

    public function removeNotification(Notifications $notification): self
    {
        if ($this->notifications->contains($notification)) {
            $this->notifications->removeElement($notification);
            $notification->removeCourse($this);
        }

        return $this;
    }

}
