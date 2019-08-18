<?php
namespace App\Controller;

use FOS\RestBundle\Controller\AbstractFOSRestController;
use App\Repository\YearRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class YearController extends AbstractFOSRestController {
    private $yearRepository;
    private $entityManager;

    public function __construct(YearRepository $yearRepository, EntityManagerInterface $entityManager)
    {
        $this->yearRepository = $yearRepository;
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/years", methods="GET")
     * @return JsonResponse
     */
    public function getAll()
    {
       $years = $this->yearRepository->findAll();
       return $this->view($years, Response::HTTP_CREATED);
    }
}