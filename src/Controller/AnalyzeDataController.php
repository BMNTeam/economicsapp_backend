<?php
namespace App\Controller;

use App\AddData\Analyze\AnalyzeDataRequest;
use App\Repository\CultureRepository;
use App\Repository\FarmCategoryRepository;
use App\Repository\StatInfoRepository;
use App\Repository\StatTypeRepository;
use App\Repository\YearRepository;
use App\Repository\MunicipalityRepository;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AnalyzeDataController extends AbstractFOSRestController
{
    private $yearRepository;
    private $cultureRepository;
    private $statTypeRepository;
    private $statInfoRepository;
    private $farmCategoryRepository;
    private $municipalityRepository;


    public function __construct(
        YearRepository $yearRepository,
        CultureRepository $cultureRepository,
        FarmCategoryRepository $farmCategoryRepository,
        StatInfoRepository $statInfoRepository,
        StatTypeRepository $statTypeRepository,
        MunicipalityRepository $municipalityRepository)
    {
        $this->yearRepository = $yearRepository;
        $this->cultureRepository = $cultureRepository;
        $this->statTypeRepository = $statTypeRepository;
        $this->statInfoRepository = $statInfoRepository;
        $this->farmCategoryRepository = $farmCategoryRepository;
        $this->municipalityRepository = $municipalityRepository;
    }

    /**
     * @Route("/analyze", methods="GET")
     * @param Request $request
     * @return \FOS\RestBundle\View\View
     */
    public function getGraphData(Request $request)
    {
        $analyze = new AnalyzeDataRequest($request, $this->statInfoRepository, $this->cultureRepository, $this->municipalityRepository, $this->statTypeRepository, $this->farmCategoryRepository, $this->yearRepository);
        return $this->view($analyze->getChartData(), Response::HTTP_OK);
    }
}