<?php
namespace App\AddData\Statistics;

use App\Entity\Municipality;
use App\Entity\StatInfo;
use App\Repository\StatTypeRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\LazyCriteriaCollection;

class ShortStatistics {
    private $data_collection;
    private $count;
    private $municipality;
    private $statTypeRepository;

    public function __construct(StatTypeRepository $statTypeRepository, LazyCriteriaCollection $data_collection, Municipality $municipality,  int $count)
    {
        $this->data_collection = $data_collection;
        $this->statTypeRepository = $statTypeRepository;
        $this->municipality = $municipality;
        $this->count = $count;
    }

    /**
     * @return ShortStatisticsResult
     */
    public function get()
    {
        $gross_fee_type = $this->statTypeRepository->find(1);
        $cultivation_area_type = $this->statTypeRepository->find(2);
        $productivity_type = $this->statTypeRepository->find(3);
        /** @var $data StatInfo[] */
        $municipality_criteria = new Criteria();
        $municipality_criteria->where($municipality_criteria->expr()->eq("municipalities", $this->municipality));
        $data = $this->data_collection->matching($municipality_criteria)->toArray();

        $gross_fee_data = $this->getSum($this->filterByType($data, $gross_fee_type->getId()));
        $cultivation_area_data = $this->getSum($this->filterByType($data, $cultivation_area_type->getId()));
        $productivity_data = $this->getSum($this->filterByType($data, $productivity_type->getId()));

        return new ShortStatisticsResult($gross_fee_data, $cultivation_area_data, $productivity_data, $this->count);
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