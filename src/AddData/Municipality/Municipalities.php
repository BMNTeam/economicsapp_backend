<?php
namespace App\AddData\Municipality;

use App\Entity\Culture;
use App\Entity\CultureType;
use App\Entity\Municipality;
use App\Entity\StatInfo;

class Municipalities {
    private $municipalities;
    private $statInfo;

    /**
     * CulturesResponse constructor.
     * @param Culture[] $municipalities
     * @param array|null $statInfo
     */
    public function __construct(array $municipalities, ?array $statInfo)
    {
        $this->municipalities = $municipalities;
        $this->statInfo = $statInfo;
    }

    public function get()
    {
        $municipalitiesWithEmptyData = $this->getMunicipalitiesWithEmptyData();
        $municipalitiesWithFilledData = $this->getMunicipalitiesWithFilledData($municipalitiesWithEmptyData);
        return $municipalitiesWithFilledData;
    }

    private function getMunicipalitiesWithEmptyData()
    {
        $municipalities_with_empty_data = array_map(function (Municipality $municipality){
            return new MunicipalityWithData($municipality->getId(), $municipality->getName(), null);
        },$this->municipalities);
        $municipalities_with_empty_data_without_region = array_filter($municipalities_with_empty_data, function (MunicipalityWithData $municipalityWithData) {
            return $municipalityWithData->id !== 27;
        });
        return $municipalities_with_empty_data_without_region;
    }

    private function getMunicipalitiesWithFilledData(array $municipalityWithEmptyData)
    {
        return array_map(function (MunicipalityWithData $municipalityWithData) {
            if(!$this->statInfo) {return $municipalityWithData; };
            $statInfo = array_filter($this->statInfo, function (StatInfo $statInfo) use ($municipalityWithData) {
                return $statInfo->getMunicipalities()->getId() === $municipalityWithData->id;
            });

            if(!$statInfo) { return $municipalityWithData; }
            $municipalityWithData->value = array_slice($statInfo, 0, 1)[0]->getValue();
            return $municipalityWithData;
        },$municipalityWithEmptyData);
    }

}