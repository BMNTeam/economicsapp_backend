<?php
namespace App\AddData\Municipality;

use App\Entity\Culture;
use App\Entity\FarmCategory;
use App\Entity\Municipality;
use App\Entity\StatType;
use App\Entity\Year;

class MunicipalitiesResponse {
    public $years;
    public $farmCategory;
    public $municipalities;
    public $statType;
    public $culture;

    /**
     * CulturesResponse constructor.
     * @param Year[] $years
     * @param FarmCategory $farmCategory
     * @param $culture
     * @param $statType
     * @param Municipality[] $municipalities
     */
    public function __construct(array $years,FarmCategory $farmCategory, Culture $culture, StatType $statType, array $municipalities)
    {
        $this->farmCategory = $farmCategory;
        $this->years = $years;
        $this->culture = $culture;
        $this->statType = $statType;
        $this->municipalities = $municipalities;
    }


}