<?php
namespace App\AddData\Culture;

use App\Entity\Municipality;
use App\Entity\StatType;
use App\Entity\Year;

class CulturesResponse {
    public $years;
    public $municipality;
    public $statType;
    public $cultures;

    /**
     * CulturesResponse constructor.
     * @param Year[] $years
     * @param $municipality
     * @param $statType
     * @param Cultures[] $cultures
     */
    public function __construct(array $years, Municipality $municipality, StatType $statType, array $cultures)
    {
        $this->years = $years;
        $this->municipality = $municipality;
        $this->statType = $statType;
        $this->cultures = $cultures;
    }


}