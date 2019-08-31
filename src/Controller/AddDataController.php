<?php
namespace App\Controller;

use App\AddData\Culture\AddCultureRequest;
use App\AddData\Culture\Cultures;
use App\AddData\Culture\CulturesResponse;
use App\AddData\Culture\CulturesByYear;

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

class AddDataController extends AbstractFOSRestController {
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
       $municipalities = $this->municipalityRepository->findAll();
       $farmCategories = $this->farmCategoryRepository->findAll();
       $statTypes = $this->statTypeRepository->findAll();

       $resp = new OptionsResponse($years, $municipalities, $farmCategories, $statTypes);

       return $this->view($resp, Response::HTTP_CREATED);
    }


    /**
     * @Route("/add-data/cultures", methods="GET")
     * @param Request $request
     * @return \FOS\RestBundle\View\View
     */
    public function getCulturesWithData( Request $request)
    {
        $municipality_id = $request->query->get("municipalityId");
        $year_id = $request->query->get("yearId");
        $stat_type = $request->query->get('statType');
        if(!$municipality_id || !$year_id || !$stat_type)
        {
            return $this->view(null,  Response::HTTP_BAD_REQUEST);
        }

        $statInfo = $this->getStatInfoData($municipality_id, $year_id, $stat_type);
        $cultures = $this->cultureRepository->findAll();
        $cultureTypes = $this->cultureTypeRepository->findAll();

        $cultures = new Cultures($cultures, $statInfo, $cultureTypes);
        $response = new CulturesResponse(
            [$this->yearRepository->find($year_id)],
            $this->municipalityRepository->find($municipality_id),
            $this->statTypeRepository->find($stat_type),
            $cultures->get()
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
        $data = new AddCultureRequest($request);
        /* @var $cultures_data CulturesByYear */
        foreach ($data->data as $cultures_data) {
            foreach ($cultures_data->cultures as $culture){
                $instance = $this->statInfoRepository->findOneBy([
                    "year" => $cultures_data->yearId,
                    "municipalities" => $data->municipality_id,
                    "stat_type" => $data->municipality_id,
                    "culture" => $culture->id
                ]);
                if($instance) {
                    $value = isset($culture->value) ? $culture->value : null;
                    $instance->setValue($value);
                    $this->entityManager->flush();
                    continue;
                }
                $statInfo = $this->createInfoIfNotExist($culture, $data, $cultures_data);

                $this->entityManager->persist($statInfo);
                $this->entityManager->flush();
            }
        }
        return $this->view(Response::HTTP_OK);
    }

    private function createInfoIfNotExist($culture, $data, $cultures_data)
    {
        $statInfo = new StatInfo();
        $cultureObj = $this->cultureRepository->find($culture->id);
        $municipalityObj = $this->municipalityRepository->find($data->municipality_id);
        $statTypeObj = $this->statTypeRepository->find($data->statTypeId);
        $yearObj = $this->yearRepository->find($cultures_data->yearId);
        $farmCategoryObj = $this->farmCategoryRepository->find(1);

        if(isset($culture->value) )
        {
            $statInfo->setValue($culture->value);
        }
        $statInfo->setCulture($cultureObj);
        $statInfo->setFarmCategory($farmCategoryObj);
        $statInfo->setMunicipalities($municipalityObj);
        $statInfo->setStatType($statTypeObj);
        $statInfo->setYear($yearObj);
        return $statInfo;
    }

    private function getStatInfoData(int $municipality_id, int $year_id, int $stat_type)
    {
        $criteria = ['municipalities' => $municipality_id, 'year' => $year_id, 'stat_type' => $stat_type];
        return $this->statInfoRepository->findBy($criteria);
    }
}

class OptionsResponse {
    public $years;
    public $municipalities;
    public $farmCategories;
    public $statTypes;

    /**
     * OptionsResponse constructor.
     * @param $years
     * @param $municipalities
     * @param $farmCategories
     * @param $statTypes
     */
    public function __construct($years, $municipalities, $farmCategories, $statTypes)
    {
        $this->years = $years;
        $this->municipalities = $municipalities;
        $this->farmCategories = $farmCategories;
        $this->statTypes = $statTypes;
    }
}



