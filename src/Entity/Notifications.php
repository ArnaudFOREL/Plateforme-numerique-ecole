<?php

namespace App\Entity;

use App\Repository\NotificationsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=NotificationsRepository::class)
 */
class Notifications
{
    const TYPEOF = [
        0 => 'Nouveau travail rendu',
        1 => 'Réponse à un travail',
        2 => 'Nouveau cours disponible',
        3 => 'Ton travail a été vu'
    ];

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $type;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isRead;

    /**
     * @ORM\Column(type="datetime")
     */
    private $PostAt;

    /**
     * @ORM\ManyToMany(targetEntity=User::class, inversedBy="notifications")
     */
    private $user;

    /**
     * @ORM\ManyToMany(targetEntity=Works::class, inversedBy="notifications")
     */
    private $work;

    /**
     * @ORM\ManyToMany(targetEntity=Course::class, inversedBy="notifications")
     */
    private $course;

    public function __construct()
    {
        $this->user = new ArrayCollection();
        $this->work = new ArrayCollection();
        $this->course = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): ?int
    {
        return $this->type;
    }

    public function getTypeOfType() 
    {
        return self::TYPEOF[$this->type];
    }

    public function setType(int $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getIsRead(): ?bool
    {
        return $this->isRead;
    }

    public function setIsRead(bool $isRead): self
    {
        $this->isRead = $isRead;

        return $this;
    }

    public function getPostAt(): ?\DateTimeInterface
    {
        return $this->PostAt;
    }

    public function setPostAt(\DateTimeInterface $PostAt): self
    {
        $this->PostAt = $PostAt;

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getUser(): Collection
    {
        return $this->user;
    }

    public function addUser(User $user): self
    {
        if (!$this->user->contains($user)) {
            $this->user[] = $user;
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        if ($this->user->contains($user)) {
            $this->user->removeElement($user);
        }

        return $this;
    }

    /**
     * @return Collection|Works[]
     */
    public function getWork(): Collection
    {
        return $this->work;
    }

    public function addWork(Works $work): self
    {
        if (!$this->work->contains($work)) {
            $this->work[] = $work;
        }

        return $this;
    }

    public function removeWork(Works $work): self
    {
        if ($this->work->contains($work)) {
            $this->work->removeElement($work);
        }

        return $this;
    }

    /**
     * @return Collection|Course[]
     */
    public function getCourse(): Collection
    {
        return $this->course;
    }

    public function addCourse(Course $course): self
    {
        if (!$this->course->contains($course)) {
            $this->course[] = $course;
        }

        return $this;
    }

    public function removeCourse(Course $course): self
    {
        if ($this->course->contains($course)) {
            $this->course->removeElement($course);
        }

        return $this;
    }
}
