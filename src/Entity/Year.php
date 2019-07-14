<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\YearRepository")
 */
class Year
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\StatInfo", mappedBy="year")
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

    public function getName(): ?int
    {
        return $this->name;
    }

    public function setName(int $name): self
    {
        $this->name = $name;

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
            $statInfo->setYear($this);
        }

        return $this;
    }

    public function removeStatInfo(StatInfo $statInfo): self
    {
        if ($this->statInfos->contains($statInfo)) {
            $this->statInfos->removeElement($statInfo);
            // set the owning side to null (unless already changed)
            if ($statInfo->getYear() === $this) {
                $statInfo->setYear(null);
            }
        }

        return $this;
    }
}
