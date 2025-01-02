<?php

namespace App\Entity;

use App\Repository\MainRepository;
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
}
