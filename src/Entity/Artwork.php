<?php

namespace App\Entity;

use App\Repository\ArtworkRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=ArtworkRepository::class)
 */
class Artwork
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"get_artworks_collection", "get_artwork", "get_exhibitions_collection", "get_exhibition_by_id"})
     * @Assert\NotBlank
     */
    private $title;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Groups({"get_artworks_collection", "get_artwork", "get_exhibitions_collection", "get_exhibition_by_id"})
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"get_artworks_collection", "get_artwork", "get_exhibitions_collection", "get_exhibition_by_id"})
     * @Assert\NotBlank
     */
    private $picture;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)

     * @Groups({"get_artworks_collection", "get_artwork", "get_exhibitions_collection", "get_exhibition_by_id"})
     */
    private $slug;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     * @Groups({"get_exhibitions_collection", "get_exhibition_by_id"})
     */
    private $status;

    /**
     * @ORM\ManyToOne(targetEntity=Exhibition::class, inversedBy="artwork")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"get_artwork"})
     * @Assert\NotBlank
     */
    private $exhibition;

    public function __construct()
    {
        $this->status = false;
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

    public function getPicture(): ?string
    {
        return $this->picture;
    }

    public function setPicture(string $picture): self
    {
        $this->picture = $picture;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(?string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function isStatus(): ?bool
    {
        return $this->status;
    }

    public function setStatus(?bool $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getExhibition(): ?Exhibition
    {
        return $this->exhibition;
    }

    public function setExhibition(?Exhibition $exhibition): self
    {
        $this->exhibition = $exhibition;

        return $this;
    }
}
