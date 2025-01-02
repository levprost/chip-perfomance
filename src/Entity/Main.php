<?php

namespace App\Entity;

use App\Repository\MainRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MainRepository::class)]
class Main
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title_main = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $optional_content = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $background_image = null;

    /**
     * @var Collection<int, Structure>
     */
    #[ORM\OneToMany(targetEntity: Structure::class, mappedBy: 'main')]
    private Collection $structures;

    public function __construct()
    {
        $this->structures = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitleMain(): ?string
    {
        return $this->title_main;
    }

    public function setTitleMain(string $title_main): static
    {
        $this->title_main = $title_main;

        return $this;
    }

    public function getOptionalContent(): ?string
    {
        return $this->optional_content;
    }

    public function setOptionalContent(?string $optional_content): static
    {
        $this->optional_content = $optional_content;

        return $this;
    }

    public function getBackgroundImage(): ?string
    {
        return $this->background_image;
    }

    public function setBackgroundImage(?string $background_image): static
    {
        $this->background_image = $background_image;

        return $this;
    }

    /**
     * @return Collection<int, Structure>
     */
    public function getStructures(): Collection
    {
        return $this->structures;
    }

    public function addStructure(Structure $structure): static
    {
        if (!$this->structures->contains($structure)) {
            $this->structures->add($structure);
            $structure->setMain($this);
        }

        return $this;
    }

    public function removeStructure(Structure $structure): static
    {
        if ($this->structures->removeElement($structure)) {
            // set the owning side to null (unless already changed)
            if ($structure->getMain() === $this) {
                $structure->setMain(null);
            }
        }

        return $this;
    }
}
