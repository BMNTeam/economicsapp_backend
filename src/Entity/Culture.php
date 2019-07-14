<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CultureRepository")
 */
class Culture
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
     * @ORM\ManyToOne(targetEntity="App\Entity\CultureType", inversedBy="cultures")
     * @ORM\JoinColumn(nullable=false)
     */
    private $culture_type;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\StatInfo", mappedBy="culture")
     */
    private $statInfos;

    public function __construct()
    {
        $this->statInfos = new ArrayCollection();
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

    public function getCultureType(): ?CultureType
    {
        return $this->culture_type;
    }

    public function setCultureType(?CultureType $culture_type): self
    {
        $this->culture_type = $culture_type;

        return $this;
    }

    /**
     * @return Collection|StatInfo[]
     */
    public function getStatInfos(): Collection
    {
        return $this->statInfos;
    }

    public function addStatInfo(StatInfo $statInfo): self
    {
        if (!$this->statInfos->contains($statInfo)) {
            $this->statInfos[] = $statInfo;
            $statInfo->setCulture($this);
        }

        return $this;
    }

    public function removeStatInfo(StatInfo $statInfo): self
    {
        if ($this->statInfos->contains($statInfo)) {
            $this->statInfos->removeElement($statInfo);
            // set the owning side to null (unless already changed)
            if ($statInfo->getCulture() === $this) {
                $statInfo->setCulture(null);
            }
        }

        return $this;
    }
}
