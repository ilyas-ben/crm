<?php

namespace App\Controller;

use App\Entity\WarehouseReception;
use App\Service\ReceptionService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/warehouse-receptions')]
class ReceptionController extends AbstractController
{
    private ReceptionService $receptionService;

    public function __construct(ReceptionService $receptionService)
    {
        $this->receptionService = $receptionService;
    }

    #[Route('', methods: ['POST'])]
    public function save(Request $request): JsonResponse
    {
        $receptionJson = $request->getContent();
        return $this->json($this->receptionService->save(null,$receptionJson));

        
    }

    #[Route('/destination/{destinationId}', name: 'warehouse_receptions_by_destination', methods: ['GET'])]
    public function getReceptionsByDestinationId(int $destinationId): JsonResponse
    {
        $receptions = $this->receptionService->getReceptionsByDestinationId($destinationId);
        return $this->json($receptions);
    }

    

    #[Route('', methods: ['GET'])]
    public function getAll(): JsonResponse
    {
        return $this->json($this->receptionService->getAll());
    }
}
        