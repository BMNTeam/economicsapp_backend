<?php

namespace App\AddData\Analyze;

class AnalyzeYearData {
    public $year;
    public $data;

    /**
     * AnalyzeYearData constructor.
     * @param $year
     * @param $data
     */
    public function __construct(int $year, ?float $data = null)
    {
        $this->year = $year;
        $this->data = $data;
    }


}