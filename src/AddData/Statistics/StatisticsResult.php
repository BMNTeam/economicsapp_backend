<?php

namespace App\AddData\Statistics;

class StatisticsResult {
    public $short_statistics;
    public $last_data;

    /**
     * StatisticsResult constructor.
     * @param $short_statistics
     * @param LastDataResult[] $last_data
     */
    public function __construct(ShortStatisticsResult $short_statistics, array $last_data)
    {
        $this->short_statistics = $short_statistics;
        $this->last_data = $last_data;
    }


}