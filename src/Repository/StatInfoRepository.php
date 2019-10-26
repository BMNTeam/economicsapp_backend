<?php

namespace App\Repository;

use App\AddData\Municipality\AddMunicipalityRequest;
use App\Entity\Municipality;
use App\Entity\StatInfo;
use App\Entity\StatType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method StatInfo|null find($id, $lockMode = null, $lockVersion = null)
 * @method StatInfo|null findOneBy(array $criteria, array $orderBy = null)
 * @method StatInfo[]    findAll()
 * @method StatInfo[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StatInfoRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, StatInfo::class);
    }

    public function setUpProductivityAndRegionData(AddMunicipalityRequest $data)
    {
        $stat_type_id = $data->statTypeId;

        $statInfos = $this->getStatInfosByMunicipalityRequest($data);
        $this->setFullRegionData($statInfos, $stat_type_id);
        $this->setProductivity($statInfos);
        $this->setFullRegionData($statInfos, 3);
    }

    private function getStatInfosByMunicipalityRequest(AddMunicipalityRequest $data)
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery('
        SELECT s
        FROM App\Entity\StatInfo s
        WHERE (s.year = :year_id
        AND s.culture = :culture_id
        AND s.farm_category = :farm_category_id
        )')->setParameters([
            'year_id' => $data->getData()[0]->yearId,
            'culture_id' => $data->culture_id,
            'farm_category_id' => $data->farm_category_id
        ]);
        return $statInfos = $query->getResult();
    }

    /**
     * @param StatInfo[] $statInfos
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    private function setProductivity(array $statInfos)
    {
        $firstStatInfo = reset($statInfos);

        $gross = array_filter($statInfos, function (StatInfo $statInfo) {
            return $statInfo->getStatType()->getId() === 1;
        });

        $cultivation_area = array_filter($statInfos, function (StatInfo $statInfo) {
            return $statInfo->getStatType()->getId() === 2;
        });

        $entityManager = $this->getEntityManager();
        $productivityStatType = $entityManager->getRepository(StatType::class)->find(3);

        $productivities = $entityManager->getRepository(StatInfo::class)->findBy([
            'year' => $firstStatInfo->getYear(),
            'stat_type' => $productivityStatType,
            'farm_category' => $firstStatInfo->getFarmCategory(),
            'culture' => $firstStatInfo->getCulture()
        ]);
        if ($productivities) {
            $productivity = $this->getValuesForProductivity($productivities, $gross, $cultivation_area);
            $entityManager->flush($productivity);
            return;
        }
        $productivity_stat_infos = $this->getNewProductivityStatInfos($gross, $cultivation_area, $productivityStatType);
        foreach ($productivity_stat_infos as $productivity_stat_info) {
            $entityManager->persist($productivity_stat_info);
        }
        $entityManager->flush();

    }

    /**
     * @param array StatInfo[] $productivities
     * @param array StatInfo[] $gross
     * @param array StatInfo[] $cultivation_area
     * @return array
     */
    private function getValuesForProductivity(array $productivities, array $gross, array $cultivation_area)
    {
        return array_map(function (StatInfo $stat) use ($gross, $cultivation_area) {
            /* @var $gross_elem StatInfo | null
             * @var $cultivation_area_elem StatInfo | null
             */
            $gross_elem = $this->getElementFromGrossOrCultivation($gross, $stat);
            $cultivation_area_elem = $this->getElementFromGrossOrCultivation($cultivation_area, $stat);
            if (!($gross_elem && $cultivation_area_elem)) {
                return $stat;
            }
            if ($gross_elem->getValue() === null || $cultivation_area_elem->getValue() === null) {
                return $stat;
            }
            $stat->setValue($gross_elem->getValue() * 10 / $cultivation_area_elem->getValue());
            return $stat;
        }, $productivities);
    }

    /**
     * @param array $gross
     * @param array $cultivation_area
     * @param StatType $statType
     * @return StatInfo[] array
     */
    private function getNewProductivityStatInfos(array $gross, array $cultivation_area, StatType $statType)
    {
        $maximum_data_arr = count($gross) >= count($cultivation_area) ? $gross : $cultivation_area;
        $maximum_data_without_region_data = array_filter($maximum_data_arr, function (StatInfo $statInfo) {
            return $statInfo->getMunicipalities()->getId() !== 27;
        });
        $productivity_stat_infos = array_map(function (StatInfo $statInfo) use ($statType) {
            $productivity_stat_info = new StatInfo();
            $productivity_stat_info->setCulture($statInfo->getCulture());
            $productivity_stat_info->setFarmCategory($statInfo->getFarmCategory());
            $productivity_stat_info->setMunicipalities($statInfo->getMunicipalities());
            $productivity_stat_info->setYear($statInfo->getYear());
            $productivity_stat_info->setStatType($statType);
            return $productivity_stat_info;
        }, $maximum_data_without_region_data);
        return $productivity_stat_infos;
    }

    private function getElementFromGrossOrCultivation(array $find_in, StatInfo $compare)
    {
        $elem = array_filter($find_in, function (StatInfo $stat) use ($compare) {
            return $compare->getMunicipalities()->getId() === $stat->getMunicipalities()->getId() &&
                $compare->getYear()->getId() === $stat->getYear()->getId() &&
                $compare->getCulture()->getId() === $stat->getCulture()->getId() &&
                $compare->getFarmCategory()->getId() === $stat->getFarmCategory()->getId();
        });
        return reset($elem);
    }

    /**
     * @param StatInfo[] $statInfos
     * @param int $stat_type_id
     * @return void
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    private function setFullRegionData(array $statInfos, int $stat_type_id)
    {
        $entityManager = $this->getEntityManager();

        $statInfo = array_filter($statInfos, function (StatInfo $info) use ($stat_type_id) {
            return $info->getStatType()->getId() === $stat_type_id;
        });
        $firstStatInfo = reset($statInfo);

        /** @var Municipality $region_municipality */
        $region_municipality = $entityManager->getRepository(Municipality::class)->find(27, 0);

        $sum = $this->getMunicipalitiesSum($statInfo, $region_municipality);

        $instance = $entityManager->getRepository(StatInfo::class)->findOneBy([
            'year' => $firstStatInfo->getYear(),
            'municipalities' => $region_municipality,
            'stat_type' => $firstStatInfo->getStatType(),
            'farm_category' => $firstStatInfo->getFarmCategory(),
            'culture' => $firstStatInfo->getCulture()
        ]);
        if (!$instance) {
            $region_data = $this->getNewRegionData($firstStatInfo, $region_municipality, $sum);
            $entityManager->persist($region_data);
            $entityManager->flush();
            return;
        }
        /**
         * @var StatInfo $instance
         */
        $instance->setValue($sum);
        $entityManager->flush();
    }

    /***
     * @param array StatInfo[] $statInfo
     * @param Municipality $region_municipality
     * @return mixed
     */
    private function getMunicipalitiesSum(array $statInfo, Municipality $region_municipality)
    {
        return array_reduce($statInfo,
            function (float $acc, StatInfo $stat) use ($region_municipality) {
                if ($stat->getMunicipalities()->getId() === $region_municipality->getId()) {
                    return $acc;
                };
                $value = $stat->getValue() === "NULL" ? 0 : $stat->getValue();
                $acc += $value;
                return $acc;
            }, 0);
    }


    private function getNewRegionData(StatInfo $firstStatInfo, Municipality $region_municipality, float $sum)
    {
        $region_data = new StatInfo();
        $region_data->setYear($firstStatInfo->getYear());
        $region_data->setStatType($firstStatInfo->getStatType());
        $region_data->setFarmCategory($firstStatInfo->getFarmCategory());
        $region_data->setCulture($firstStatInfo->getCulture());
        $region_data->setMunicipalities($region_municipality);
        $region_data->setValue($sum);
        return $region_data;
    }


    // /**
    //  * @return StatInfo[] Returns an array of StatInfo objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?StatInfo
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
