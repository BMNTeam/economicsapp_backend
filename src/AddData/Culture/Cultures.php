<?php
namespace App\AddData\Culture;

use App\Entity\Culture;
use App\Entity\CultureType;
use App\Entity\StatInfo;

class Cultures {
    private $cultures;
    private $cultureTypes;
    private $statInfo;

    /**
     * CulturesResponse constructor.
     * @param Culture[] $cultures
     * @param array|null $statInfo
     * @param CultureType[] $cultureTypes
     */
    public function __construct(array $cultures, ?array $statInfo, array $cultureTypes)
    {
        $this->cultures = $cultures;
        $this->cultureTypes = $cultureTypes;
        $this->statInfo = $statInfo;
    }

    public function get()
    {
        $culturesWithEmptyData = $this->getCulturesWithEmptyData();
        $culturesWithFilledData = $this->getCulturesWithFilledData($culturesWithEmptyData);
        return $this->getGroupedByCultureType($culturesWithFilledData);
    }

    private function getCulturesWithEmptyData()
    {
        return array_map(function (Culture $culture){
            return new CultureWithData($culture->getId(), $culture->getName(), $culture->getCultureType()->getId(), null);
        },$this->cultures);
    }

    private function getCulturesWithFilledData(array $cultureWithEmptyData)
    {
        return array_map(function (CultureWithData $cultureWithData) {
            if(!$this->statInfo) {return $cultureWithData; };
            $statInfo = array_filter($this->statInfo, function (StatInfo $statInfo) use ($cultureWithData) {
                return $statInfo->getCulture()->getId() === $cultureWithData->id;
            });

            if(!$statInfo) { return $cultureWithData; }
            $cultureWithData->value = array_slice($statInfo, 0, 1)[0]->getValue();
            return $cultureWithData;
        },$cultureWithEmptyData);
    }

    private function getGroupedByCultureType(array $culturesWithFilledData)
    {
        return array_reduce($this->cultureTypes,
            function (array $carry, CultureType $cultureType) use ($culturesWithFilledData) {
            $culture_type_id = $cultureType->getId();
            $children = array_filter($culturesWithFilledData,
                function (CultureWithData $cultureWithData) use ($culture_type_id) {
                    return $cultureWithData->culture_type === $culture_type_id;
                });
            return array_merge($carry, [$cultureType->getName() => $children ]);
        }, []);
    }

}