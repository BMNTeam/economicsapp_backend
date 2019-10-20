<?php
namespace App\AddData\Analyze;

use App\Entity\StatInfo;
use App\Entity\Year;

class Analyze
{
    private $years;
    private $statInfos;

    /**
     * AnalyzeResponse constructor.
     * @param Year[] $years
     * @param StatInfo[] $statInfos
     */
    public function __construct($years, $statInfos)
    {
        $this->years = $years;
        $this->statInfos = $statInfos;
    }

    public function get()
    {
        return new AnalyzeResponse($this->generateGraphDataByYears());
    }

    private function generateGraphDataByYears()
    {
        return array_map(function (Year $year){
            /** @var StatInfo[] $data_for_year */
            $data_for_year = array_filter($this->statInfos, function (StatInfo $statInfo) use ($year){
                return $statInfo->getYear()->getId() === $year->getId();
            });
            if(!$data_for_year){
                return new AnalyzeYearData($year->getName());
            }
            return new AnalyzeYearData($year->getName(), reset($data_for_year)->getValue());
        }, $this->years);
    }


}