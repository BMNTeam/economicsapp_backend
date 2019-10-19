<?php

namespace App\Controller;

use App\AddData\Municipality\AddCultureRequest;
use App\AddData\Municipality\AddMunicipalityRequest;
use App\AddData\Municipality\CulturesByYear;

use App\AddData\Municipality\Municipalities;
use App\AddData\Municipality\MunicipalitiesByYear;
use App\AddData\Municipality\MunicipalitiesResponse;
use App\Entity;
use App\Entity\StatInfo;

use App\Repository\CultureRepository;
use App\Repository\CultureTypeRepository;
use App\Repository\FarmCategoryRepository;
use App\Repository\MunicipalityRepository;
use App\Repository\StatInfoRepository;
use App\Repository\StatTypeRepository;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use App\Repository\YearRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class AddDataController extends AbstractFOSRestController
{
    private $cultureRepository;
    private $cultureTypeRepository;
    private $yearRepository;
    private $municipalityRepository;
    private $farmCategoryRepository;
    private $statTypeRepository;
    private $statInfoRepository;
    private $entityManager;

    public function __construct(YearRepository $yearRepository,
                                MunicipalityRepository $municipalityRepository,
                                FarmCategoryRepository $farmCategoryRepository,
                                StatTypeRepository $statTypeRepository,
                                StatInfoRepository $statInfoRepository,
                                CultureRepository $cultureRepository,
                                CultureTypeRepository $cultureTypeRepository,
                                EntityManagerInterface $entityManager)
    {
        $this->yearRepository = $yearRepository;
        $this->municipalityRepository = $municipalityRepository;
        $this->farmCategoryRepository = $farmCategoryRepository;
        $this->statTypeRepository = $statTypeRepository;
        $this->statInfoRepository = $statInfoRepository;
        $this->cultureRepository = $cultureRepository;
        $this->cultureTypeRepository = $cultureTypeRepository;
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/add-data/options", methods="GET")
     * @return JsonResponse
     */
    public function getAllOptions()
    {
        $years = $this->yearRepository->findAll();
        $farmCategories = $this->farmCategoryRepository->findAll();
        $statTypes = $this->statTypeRepository->findAll();
        $cultures = $this->cultureRepository->findAll();
        $municipalities = $this->municipalityRepository->findAll();

        $resp = new OptionsResponse($years, $cultures, $farmCategories, $statTypes, $municipalities);

        return $this->view($resp, Response::HTTP_CREATED);
    }


    /**
     * @Route("/add-data/municipalities", methods="GET")
     * @param Request $request
     * @return \FOS\RestBundle\View\View
     */
    public function getMunicipalitiesWithData(Request $request)
    {
        $culture_id = $request->query->get("cultureId");
        $year_id = $request->query->get("yearId");
        $stat_type = $request->query->get('statType');
        $farm_category_id = $request->query->get("farmCategory");
        if (!$culture_id || !$year_id || !$stat_type || !$farm_category_id) {
            return $this->view(null, Response::HTTP_BAD_REQUEST);
        }

        $statInfo = $this->getStatInfoData($farm_category_id, $year_id, $stat_type, $culture_id);
        $municipalities = $this->municipalityRepository->findAll();

        $municipalities = new Municipalities($municipalities, $statInfo);
        $response = new MunicipalitiesResponse(
            [$this->yearRepository->find($year_id)],
            $this->farmCategoryRepository->find($farm_category_id),
            $this->cultureRepository->find($culture_id),
            $this->statTypeRepository->find($stat_type),
            $municipalities->get()
        );
        return $this->view($response, Response::HTTP_OK);
    }

    /**
     * @Route("/add-data", methods="PUT")
     * @param Request $request
     * @return \FOS\RestBundle\View\View
     */
    function addData(Request $request)
    {
        $data = new AddMunicipalityRequest($request);
        /* @var $municipalities_data MunicipalitiesByYear */
        foreach ($data->data as $municipalities_data) {
            foreach ($municipalities_data->municipalities as $municipality) {
                $manager = $this->getDoctrine()->getManager();
                $instance = $manager->getRepository(StatInfo::class)->findOneBy([
                    "year" => $municipalities_data->yearId,
                    "culture" => $data->culture_id,
                    "farm_category" => $data->farm_category_id,
                    "stat_type" => $data->statTypeId,
                    "municipalities" => $municipality->id
                ]);
                if ($instance) {
                    $value = isset($municipality->value) ? $municipality->value : null;
                    $instance->setValue($value);
                    $manager->flush();
                    // $this->trySetProductivity($instance, $data);
                    continue;
                }
                $statInfo = $this->createInfoIfNotExist($municipality, $data, $municipalities_data);
                $manager->persist($statInfo);
                $manager->flush();
                $this->trySetProductivity($statInfo, $data);

            }
        }
        return $this->view(Response::HTTP_OK);
    }

    private function trySetProductivity(StatInfo $statInfo, $data)
    {
        $statType = $statInfo->getStatType();
        $manager = $this->getDoctrine()->getManager();

        $oppositeStatTypeId = $statType->getId() === 1 ? 2 : 1;
        $oppositeStatInfo = $manager->getRepository(StatInfo::class)->findOneBy([
            'year' => $statInfo->getYear(),
            "culture" => $statInfo->getCulture(),
            "farm_category" => $statInfo->getFarmCategory(),
            "stat_type" => $this->statTypeRepository->find($oppositeStatTypeId),
            "municipalities" => $statInfo->getMunicipalities()
        ]);

        if(!($oppositeStatInfo && $oppositeStatInfo->getValue())) return;
        $value = $oppositeStatInfo->getId() === 1
            ? $oppositeStatInfo->getValue()/ $statInfo->getValue()
            : $statInfo->getValue() / $oppositeStatInfo->getValue();

        $statInfo = $manager->getRepository(StatInfo::class)->find($statInfo->getId());
        $productivityInfo = $this->getProductivityOrCreateIfNotExists($statInfo, $data);
        $productivityInfo->setValue($value);
        $manager->persist($productivityInfo);
        $manager->flush();
        return $manager;

    }
    private function getProductivityOrCreateIfNotExists(StatInfo $statInfo, $data)
    {
        $productivityInfo = $this->statInfoRepository->findOneBy([
            'year' => $statInfo->getYear(),
            "culture" => $statInfo->getCulture(),
            "farm_category" => $statInfo->getFarmCategory(),
            "stat_type" => $this->statTypeRepository->find(3),
            "municipalities" => $statInfo->getMunicipalities()
        ]);
        $data->statTypeId = 3;
        $municipality = (object)['id' => $statInfo->getMunicipalities()->getId()];
        $year = (object)['yearId' => $statInfo->getYear()->getId()];
        if(!$productivityInfo) {
            return $this->createInfoIfNotExist($municipality, $data, $year);
        }
        return $productivityInfo;
    }

    /**
     * @param $municipality
     * @param $data
     * @param $cultures_data
     * @return StatInfo
     */
    private function createInfoIfNotExist($municipality, $data, $cultures_data)
    {
        $statInfo = new StatInfo();
        $municipalityObj = $this->municipalityRepository->find($municipality->id);
        $cultureObj = $this->cultureRepository->find($data->culture_id);
        $statTypeObj = $this->statTypeRepository->find($data->statTypeId);
        $yearObj = $this->yearRepository->find($cultures_data->yearId);
        $farmCategoryObj = $this->farmCategoryRepository->find($data->farm_category_id);

        if (isset($municipality->value)) {
            $statInfo->setValue($municipality->value);
        }
        $statInfo->setCulture($cultureObj);
        $statInfo->setFarmCategory($farmCategoryObj);
        $statInfo->setMunicipalities($municipalityObj);
        $statInfo->setStatType($statTypeObj);
        $statInfo->setYear($yearObj);
        return $statInfo;
    }

    private function getStatInfoData(int $farm_category_id, int $year_id, int $stat_type, int $culture_id)
    {
        $criteria = ['farm_category' => $farm_category_id, 'year' => $year_id, 'stat_type' => $stat_type, 'culture' => $culture_id];
        return $this->statInfoRepository->findBy($criteria);
    }
}

class OptionsResponse
{
    public $cultures;
    public $years;
    public $farmCategories;
    public $statTypes;
    public $regions;

    /**
     * OptionsResponse constructor.
     * @param $years
     * @param $cultures
     * @param $farmCategories
     * @param $statTypes
     * @param $regions
     */
    public function __construct($years, $cultures, $farmCategories, $statTypes, $regions)
    {
        $this->years = $years;
        $this->cultures = $cultures;
        $this->farmCategories = $farmCategories;
        $this->statTypes = $statTypes;
        $this->regions = $regions;
    }
}



