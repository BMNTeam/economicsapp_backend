<?php
namespace App\AddData\Statistics;

use App\Entity\StatInfo;
use App\Entity\StatType;
use App\Repository\StatTypeRepository;
use Doctrine\Common\Collections\ArrayCollection;

class ShortStatistics {
    private $data_collection;
    private $count;
    private $statTypeRepository;

    public function __construct(StatTypeRepository $statTypeRepository, ArrayCollection $data_collection, int $count)
    {
        $this->data_collection = $data_collection;
        $this->statTypeRepository = $statTypeRepository;
        $this->count = $count;
    }

    /**
     * @return ShortStatisticsResult
     */
    public function get()
    {
        $gross_fee_type = $this->statTypeRepository->find(1);
        $cultivation_area_type = $this->statTypeRepository->find(2);
        /** @var $data StatInfo[] */
        $data = $this->data_collection->toArray();

        $gross_fee_data = $this->filterByType($data, $gross_fee_type->getId());
        $gross_fee_sum = $this->getSum($gross_fee_data);

        $cultivation_area_data = $this->filterByType($data, $cultivation_area_type->getId());
        $cultivation_area_sum = $this->getSum($cultivation_area_data);

        $productivity_sum = $gross_fee_sum / $cultivation_area_sum;

        return new ShortStatisticsResult($gross_fee_sum, $cultivation_area_sum, $productivity_sum, $this->count);
    }

    /**
     * @param StatInfo[] $data
     * @return float
     */
    private function getSum(array $data)
    {
        return array_reduce($data, function ($acc, StatInfo $statInfo) {
            return $acc + $statInfo->getValue();
        }, 0);
    }

    /**
     * @param StatInfo[] $data
     * @param int $id
     * @return StatInfo[]
     */

    private function filterByType(array $data, int $id)
    {
        return array_filter($data, function (StatInfo $statInfo) use ($id) {
            return $statInfo->getStatType()->getId() === $id;
        });
    }
}