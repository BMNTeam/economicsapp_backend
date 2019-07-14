<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\StatTypeRepository")
 */
class StatType
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
     * @ORM\Column(type="string", length=255)
     */
    private $unit;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\StatInfo", mappedBy="stat_type")
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

    public function getUnit(): ?string
    {
        return $this->unit;
    }

    public function setUnit(string $unit): self
    {
        $this->unit = $unit;

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
            $statInfo->setStatType($this);
        }

        return $this;
    }

    public function removeStatInfo(StatInfo $statInfo): self
    {
        if ($this->statInfos->contains($statInfo)) {
            $this->statInfos->removeElement($statInfo);
            // set the owning side to null (unless already changed)
            if ($statInfo->getStatType() === $this) {
                $statInfo->setStatType(null);
            }
        }

        return $this;
    }
}
