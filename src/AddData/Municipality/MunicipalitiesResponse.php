<?php
namespace App\AddData\Municipality;

use App\Entity\Culture;
use App\Entity\Municipality;
use App\Entity\StatType;
use App\Entity\Year;

class MunicipalitiesResponse {
    public $years;
    public $municipality;
    public $statType;
    public $culture;

    /**
     * CulturesResponse constructor.
     * @param Year[] $years
     * @param $culture
     * @param $statType
     * @param Municipality[] $municipalities
     */
    public function __construct(array $years, Culture $culture, StatType $statType, array $municipalities)
    {
        $this->years = $years;
        $this->culture = $culture;
        $this->statType = $statType;
        $this->municipality = $municipalities;
    }


}