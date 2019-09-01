<?php

namespace App\AddData\Statistics;

class LastDataResult {
    public $id;
    public $year;
    public $municipality;
    public $culture;
    public $stat_type;
    public $value;

    /**
     * LastDataResult constructor.
     * @param $id
     * @param $year
     * @param $municipality
     * @param $culture
     * @param $stat_type
     * @param $value
     */
    public function __construct(int $id, int $year, string $municipality, string $culture, string $stat_type, float $value)
    {
        $this->id = $id;
        $this->year = $year;
        $this->municipality = $municipality;
        $this->culture = $culture;
        $this->stat_type = $stat_type;
        $this->value = $value;
    }
}