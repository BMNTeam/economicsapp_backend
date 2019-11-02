<?php

namespace App\AddData\Analyze;

use App\Repository\CultureRepository;
use App\Repository\FarmCategoryRepository;
use App\Repository\MunicipalityRepository;
use App\Repository\StatInfoRepository;
use App\Repository\StatTypeRepository;
use App\Repository\YearRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\LazyCriteriaCollection;
use Symfony\Component\HttpFoundation\Request;

class AnalyzeDataRequest {
    public $culture;
    public $municipality;
    public $stat_type;
    public $farm_category;
    private $statInfoRepository;
    private $years;

    /**
     * AnalyzeDataRequest constructor.
     * @param $request Request
     * @param StatInfoRepository $statInfoRepository
     * @param CultureRepository $cultureRepository
     * @param MunicipalityRepository $municipalityRepository
     * @param StatTypeRepository $statTypeRepository
     * @param FarmCategoryRepository $farmCategoryRepository
     * @param YearRepository $yearRepository
     */
    public function __construct(Request $request, StatInfoRepository $statInfoRepository, CultureRepository $cultureRepository, MunicipalityRepository $municipalityRepository, StatTypeRepository $statTypeRepository, FarmCategoryRepository $farmCategoryRepository, YearRepository $yearRepository)
    {
        $this->culture = $this->getCulture($cultureRepository, $request->query->get("cultureId"));
        $this->municipality = $this->getMunicipality($municipalityRepository, $request->query->get("municipalityId"));
        $this->stat_type = $this->getStatType($statTypeRepository, $request->query->get("statTypeId"));
        $this->farm_category = $this->getFarmCategory($farmCategoryRepository, $request->query->get("farmCategoryId"));
        $this->years = $this->getAllYears($yearRepository, 1999);
        $this->statInfoRepository = $statInfoRepository;
    }

    public function getChartData()
    {

        /*** $data StatInfo[] */
        $data = $this->statInfoRepository->matching($this->getBasicCriteria())->toArray();
        $analyze = new Analyze($this->years, $data, $this->stat_type);
        return $analyze->get();
    }

    /***
     * @return Criteria
     */
    private function getBasicCriteria()
    {
        $criteria = new Criteria();
        $criteria->where($criteria->expr()->gt("value", 0));
        $criteria->andWhere($criteria->expr()->eq("culture", $this->culture));
        $criteria->andWhere($criteria->expr()->eq("farm_category", $this->farm_category));
        $criteria->andWhere($criteria->expr()->eq("municipalities", $this->municipality));
        $criteria->andWhere($criteria->expr()->eq("stat_type", $this->stat_type));
        return $criteria;
    }

    private function getCulture(CultureRepository $cultureRepository, int $id)
    {
        return $cultureRepository->find($id);
    }

    private function getMunicipality(MunicipalityRepository $municipalityRepository, int $id)
    {
        return $municipalityRepository->find($id);
    }

    private function  getStatType(StatTypeRepository $statTypeRepository, int $id)
    {
        return $statTypeRepository->find($id);
    }

    private function getFarmCategory(FarmCategoryRepository $farmCategoryRepository, int $id)
    {
        return $farmCategoryRepository->find($id);
    }

    /**
     * @param YearRepository $yearRepository
     * @param int optional $yearStart
     * @return \App\Entity\Year[]
     */
    private function getAllYears(YearRepository $yearRepository, ?int $yearStart = null)
    {
        if($yearStart) {
            $criteria = new Criteria();
            $criteria->where($criteria->expr()->gt("name", $yearStart));
            return $yearRepository->matching($criteria)->toArray();
        }
        return $yearRepository->findAll();
    }
}