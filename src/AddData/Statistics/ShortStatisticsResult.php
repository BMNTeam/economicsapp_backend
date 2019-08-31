<?php
namespace App\AddData\Statistics;

class ShortStatisticsResult {
    public $gross_fee_sum;
    public $cultivation_area_sum;
    public $productivity_sum;
    public $total;

    /**
     * ShortStatistics constructor.
     * @param $gross_fee_sum
     * @param $cultivation_area_sum
     * @param $productivity_sum
     * @param $total
     */
    public function __construct($gross_fee_sum, $cultivation_area_sum, $productivity_sum, $total)
    {
        $this->gross_fee_sum = $gross_fee_sum;
        $this->cultivation_area_sum = $cultivation_area_sum;
        $this->productivity_sum = $productivity_sum;
        $this->total = $total;
    }
}