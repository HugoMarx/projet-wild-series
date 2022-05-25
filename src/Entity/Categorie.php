<?php

namespace App\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use App\Repository\CategorieRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CategorieRepository::class)]
class Categorie
{

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $name;

    #[ORM\OneToMany(mappedBy: 'categorie', targetEntity: Program::class)]
    private $programs;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function __construct()
    {
        $this->programs = new ArrayCollection();
    }

    public function getPrograms(): Collection
    {
        return $this->programs;
    }

    public function addProgram(Program $program): self
{
    if (!$this->programs->contains($program)) {
        $this->programs[] = $program;
        $program->setCategorie($this);
    }

    return $this;
}

public function removeProgram(Program $program): self
{
    if ($this->programs->removeElement($program)) {
        // set the owning side to null (unless already changed)
        if ($program->getCategorie() === $this) {
            $program->setCategorie(null);
        }
    }

    return $this;
}

}
