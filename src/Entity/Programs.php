<?php

namespace App\Entity;

use App\Repository\ProgramsRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ProgramsRepository::class)
 */
class Programs
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $cp_program1;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $cp_program2;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $ce1_program1;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $ce1_program2;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $ce1_program3;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $ce2_program1;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $ce2_program2;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $cm1_program1;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $cm1_program2;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $cm2_program1;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCpProgram1(): ?string
    {
        return $this->cp_program1;
    }

    public function setCpProgram1(?string $cp_program1): self
    {
        $this->cp_program1 = $cp_program1;

        return $this;
    }

    public function getCpProgram2(): ?string
    {
        return $this->cp_program2;
    }

    public function setCpProgram2(?string $cp_program2): self
    {
        $this->cp_program2 = $cp_program2;

        return $this;
    }

    public function getCe1Program1(): ?string
    {
        return $this->ce1_program1;
    }

    public function setCe1Program1(?string $ce1_program1): self
    {
        $this->ce1_program1 = $ce1_program1;

        return $this;
    }

    public function getCe1Program2(): ?string
    {
        return $this->ce1_program2;
    }

    public function setCe1Program2(?string $ce1_program2): self
    {
        $this->ce1_program2 = $ce1_program2;

        return $this;
    }

    public function getCe1Program3(): ?string
    {
        return $this->ce1_program3;
    }

    public function setCe1Program3(?string $ce1_program3): self
    {
        $this->ce1_program3 = $ce1_program3;

        return $this;
    }

    public function getCe2Program1(): ?string
    {
        return $this->ce2_program1;
    }

    public function setCe2Program1(?string $ce2_program1): self
    {
        $this->ce2_program1 = $ce2_program1;

        return $this;
    }

    public function getCe2Program2(): ?string
    {
        return $this->ce2_program2;
    }

    public function setCe2Program2(?string $ce2_program2): self
    {
        $this->ce2_program2 = $ce2_program2;

        return $this;
    }

    public function getCm1Program1(): ?string
    {
        return $this->cm1_program1;
    }

    public function setCm1Program1(?string $cm1_program1): self
    {
        $this->cm1_program1 = $cm1_program1;

        return $this;
    }

    public function getCm1Program2(): ?string
    {
        return $this->cm1_program2;
    }

    public function setCm1Program2(?string $cm1_program2): self
    {
        $this->cm1_program2 = $cm1_program2;

        return $this;
    }

    public function getCm2Program1(): ?string
    {
        return $this->cm2_program1;
    }

    public function setCm2Program1(?string $cm2_program1): self
    {
        $this->cm2_program1 = $cm2_program1;

        return $this;
    }
}
