<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CultureTypeRepository")
 */
class CultureType
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Culture", mappedBy="culture_type")
     */
    private $cultures;

    public function __construct()
    {
        $this->cultures = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection|Culture[]
     */
    public function getCultures(): Collection
    {
        return $this->cultures;
    }

    public function addCulture(Culture $culture): self
    {
        if (!$this->cultures->contains($culture)) {
            $this->cultures[] = $culture;
            $culture->setCultureType($this);
        }

        return $this;
    }

    public function removeCulture(Culture $culture): self
    {
        if ($this->cultures->contains($culture)) {
            $this->cultures->removeElement($culture);
            // set the owning side to null (unless already changed)
            if ($culture->getCultureType() === $this) {
                $culture->setCultureType(null);
            }
        }

        return $this;
    }
}
