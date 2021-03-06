<?php
namespace App\Controller;

use App\AddData\Statistics\LastDataResult;
use App\AddData\Statistics\ShortStatistics;
use App\AddData\Statistics\StatisticsResult;
use App\Entity\Municipality;
use App\Entity\StatInfo;
use App\Repository\CultureRepository;
use App\Repository\StatInfoRepository;
use App\Repository\StatTypeRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\LazyCriteriaCollection;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use App\Repository\YearRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractFOSRestController
{
    private $cultureRepository;
    private $statInfoRepository;
    private $entityManager;
    private $yearRepository;
    private $statTypeRepository;


    public function __construct(StatInfoRepository $statInfoRepository, EntityManagerInterface $entityManager, YearRepository $yearRepository, StatTypeRepository $statTypeRepository, CultureRepository $cultureRepository)
    {
        $this->cultureRepository = $cultureRepository;
        $this->statInfoRepository = $statInfoRepository;
        $this->entityManager = $entityManager;
        $this->yearRepository = $yearRepository;
        $this->statTypeRepository = $statTypeRepository;
    }

    /**
     * @Route("/statistics", methods="GET")
     * @param Request $request
     * @return \FOS\RestBundle\View\View
     */
    public function getStatistics(Request $request)
    {
        $year = $this->yearRepository->find($request->query->get("yearId"));
        $culture = $this->cultureRepository->find($request->query->get("cultureId"));
        $municipality = $this->getDoctrine()->getRepository(Municipality::class)->find(27, 0);

        $criteria = new Criteria();
        $criteria->where($criteria->expr()->gt("value", 0));
        $criteria->andWhere($criteria->expr()->eq("year", $year));
        $criteria->andWhere($criteria->expr()->eq("culture", $culture));

        /* @var $results StatInfo[] */
        $results = $this->statInfoRepository->matching($criteria);
        $resp = $this->getShortStatistics($results, $municipality);

        return $this->view($resp, Response::HTTP_CREATED);
    }

    /**
     * @param LazyCriteriaCollection $collection
     * @param Municipality $municipality
     * @return StatInfo[]
     */
    private function getShortStatistics(LazyCriteriaCollection $collection, Municipality $municipality)
    {
        $test = $this->transformToStatisticsData($collection, $municipality, $collection->count());
        return $test;

    }

    private function transformToStatisticsData(LazyCriteriaCollection $data_collection, Municipality $municipality, int $count)
    {

        $short_statistics = new ShortStatistics($this->statTypeRepository, $data_collection, $municipality, $count);
        $short_description = $short_statistics->get();

        $last_data = $this->getLastData($data_collection);
        return new StatisticsResult($short_description, $last_data);
    }

    private function getLastData(LazyCriteriaCollection $data_collection)
    {
        /** @var StatInfo[] $data */
       $items = array_slice($data_collection->toArray(), 0, 5);
       return array_map(function (StatInfo $item){
           return new LastDataResult(
               $item->getId(),
               $item->getYear()->getName(),
               $item->getMunicipalities()->getName(),
               $item->getCulture()->getName(),
               $item->getStatType()->getName(),
               $item->getValue());
       }, $items);
    }





}

class ShortStatistic {
    public $value;
    public $title;
    public $year;

    /**
     * ShortStatistic constructor.
     * @param $value
     * @param $title
     * @param $year
     */
    public function __construct(int $value, string $title, int $year)
    {
        $this->value = $value;
        $this->title = $title;
        $this->year = $year;
    }


}