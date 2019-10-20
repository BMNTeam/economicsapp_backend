<?php
namespace App\AddData\Analyze;

use App\Entity\Year;

class AnalyzeResponse {
    public $graph_data;

    /**
     * AnalyzeResponse constructor.
     * @param Year[] $graph_data
     */
    public function __construct(array $graph_data)
    {
        $this->graph_data = $graph_data;
    }

}