<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\StatInfoRepository")
 */
class StatInfo
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Year", inversedBy="statInfos")
     * @ORM\JoinColumn(nullable=false)
     */
    private $year;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Municipality", inversedBy="statInfos")
     */
    private $municipalities;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\FarmCategory", inversedBy="statInfos")
     * @ORM\JoinColumn(nullable=false)
     */
    private $farm_category;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Culture", inversedBy="statInfos")
     * @ORM\JoinColumn(nullable=false)
     */
    private $culture;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\StatType", inversedBy="statInfos")
     * @ORM\JoinColumn(nullable=false)
     */
    private $stat_type;

    /**
     * @ORM\Column(type="float")
     */
    private $value;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getYear(): ?Year
    {
        return $this->year;
    }

    public function setYear(?Year $year): self
    {
        $this->year = $year;

        return $this;
    }

    public function getMunicipalities(): ?Municipality
    {
        return $this->municipalities;
    }

    public function setMunicipalities(?Municipality $municipalities): self
    {
        $this->municipalities = $municipalities;

        return $this;
    }

    public function getFarmCategory(): ?FarmCategory
    {
        return $this->farm_category;
    }

    public function setFarmCategory(?FarmCategory $farm_category): self
    {
        $this->farm_category = $farm_category;

        return $this;
    }

    public function getCulture(): ?Culture
    {
        return $this->culture;
    }

    public function setCulture(?Culture $culture): self
    {
        $this->culture = $culture;

        return $this;
    }

    public function getStatType(): ?StatType
    {
        return $this->stat_type;
    }

    public function setStatType(?StatType $stat_type): self
    {
        $this->stat_type = $stat_type;

        return $this;
    }

    public function getValue(): ?float
    {
        return $this->value;
    }

    public function setValue(float $value): self
    {
        $this->value = $value;

        return $this;
    }
}
