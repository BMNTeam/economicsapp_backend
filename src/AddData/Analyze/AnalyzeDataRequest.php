<?php

namespace App\AddData\Analyze;

use App\Repository\CultureRepository;
use App\Repository\FarmCategoryRepository;
use App\Repository\MunicipalityRepository;
use App\Repository\StatInfoRepository;
use App\Repository\StatTypeRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\LazyCriteriaCollection;
use Symfony\Component\HttpFoundation\Request;

class AnalyzeDataRequest {
    public $culture;
    public $municipality;
    public $stat_type;
    public $farm_category;
    private $statInfoRepository;

    /**
     * AnalyzeDataRequest constructor.
     * @param $request Request
     * @param StatInfoRepository $statInfoRepository
     * @param CultureRepository $cultureRepository
     * @param MunicipalityRepository $municipalityRepository
     * @param StatTypeRepository $statTypeRepository
     * @param FarmCategoryRepository $farmCategoryRepository
     */
    public function __construct(Request $request, StatInfoRepository $statInfoRepository, CultureRepository $cultureRepository, MunicipalityRepository $municipalityRepository, StatTypeRepository $statTypeRepository, FarmCategoryRepository $farmCategoryRepository)
    {
        $this->culture = $this->getCulture($cultureRepository, $request->query->get("cultureId"));
        $this->municipality = $this->getMunicipality($municipalityRepository, $request->query->get("municipalityId"));
        $this->stat_type = $this->getStatType($statTypeRepository, $request->query->get("statTypeId"));
        $this->farm_category = $this->getFarmCategory($farmCategoryRepository, $request->query->get("farmCategoryId"));
        $this->statInfoRepository = $statInfoRepository;
    }

    public function getChartData()
    {

        /*** $data StatInfo[] */
        $data = $this->statInfoRepository->matching($this->getBasicCriteria())->toArray();
        return $data;
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
        return $criteria;
    }

    private function getCulture(CultureRepository $cultureRepository, int $id)
    {
        return $cultureRepository->find($id);
    }

    private function getMunicipality(MunicipalityRepository $municipalityRepository, int $id)
    {
        if ($id === 9999) // hardcoded due to all municipalities in UI
        {
            return $municipalityRepository->findAll();
        }
        return $municipalityRepository->find($id);
    }

    private function  getStatType(StatTypeRepository $statTypeRepository, int $id)
    {
        if($id === 9999) // hardcoded due to all municipalities in UI
        {
            return $statTypeRepository->findAll();
        }
        return $statTypeRepository->find($id);
    }

    private function getFarmCategory(FarmCategoryRepository $farmCategoryRepository, int $id)
    {
        return $farmCategoryRepository->find($id);
    }
}