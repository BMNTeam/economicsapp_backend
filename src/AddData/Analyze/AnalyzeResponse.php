<?php
namespace App\AddData\Analyze;

use App\Entity\StatType;
use App\Entity\Year;

class AnalyzeResponse {
    public $graph_data;
    public $stat_type;

    /**
     * AnalyzeResponse constructor.
     * @param Year[] $graph_data
     * @param StatType $stat_type
     */
    public function __construct(array $graph_data, ?StatType $stat_type)
    {
        $this->graph_data = $graph_data;
        $this->stat_type = ["name" => $stat_type->getName(), "unit" => $stat_type->getUnit()];
    }

}