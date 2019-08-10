<?php
namespace App\Controller;

use FOS\RestBundle\Controller\AbstractFOSRestController;
use App\Repository\MunicipalityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MunicipalityController extends AbstractFOSRestController {
    private $municipalityRepository;
    private $entityManager;

    public function __construct(MunicipalityRepository $municipalityRepository, EntityManagerInterface $entityManager)
    {
        $this->municipalityRepository = $municipalityRepository;
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/municipalities", methods="GET")
     * @return JsonResponse
     */
    public function getAll()
    {
        return $this->view($this->municipalityRepository->findAll(), Response::HTTP_CREATED);
    }


}