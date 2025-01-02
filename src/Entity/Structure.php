<?php

namespace App\Entity;

use App\Repository\StructureRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: StructureRepository::class)]
class Structure
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $image_structure = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $content_structure = null;

    #[ORM\ManyToOne(inversedBy: 'structures')]
    private ?Main $main = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getImageStructure(): ?string
    {
        return $this->image_structure;
    }

    public function setImageStructure(?string $image_structure): static
    {
        $this->image_structure = $image_structure;

        return $this;
    }

    public function getContentStructure(): ?string
    {
        return $this->content_structure;
    }

    public function setContentStructure(string $content_structure): static
    {
        $this->content_structure = $content_structure;

        return $this;
    }

    public function getMain(): ?Main
    {
        return $this->main;
    }

    public function setMain(?Main $main): static
    {
        $this->main = $main;

        return $this;
    }
}
