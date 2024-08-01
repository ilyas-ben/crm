<?php

namespace App\Controller;

use App\Entity\StockMovement;
use App\Service\StockMovementService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/stock-movements')]
class StockMovementController extends AbstractController
{
    private StockMovementService $stockMovementService;
    private SerializerInterface $serializer;

    public function __construct(StockMovementService $stockMovementService, SerializerInterface $serializer)
    {
        $this->stockMovementService = $stockMovementService;
        $this->serializer = $serializer;
    }

    #[Route('', methods: ['GET'])]
    public function getAll(): JsonResponse
    {
        $stockMovements = $this->stockMovementService->getAll();
        $jsonStockMovements = $this->serializer->serialize($stockMovements, 'json');

        return new JsonResponse($jsonStockMovements, JsonResponse::HTTP_OK, [], true);
    }

    #[Route('/byid/{id}', methods: ['GET'])]
    public function getById(int $id): JsonResponse
    {
        $stockMovement = $this->stockMovementService->getById($id);
        $jsonStockMovement = $this->serializer->serialize($stockMovement, 'json');

        return new JsonResponse($jsonStockMovement, JsonResponse::HTTP_OK, [], true);
    }

    #[Route('', methods: ['POST'])]
    public function save(Request $request): JsonResponse
    {
        $stockMovementJson = $request->getContent();
        $stockMovement = $this->stockMovementService->save(null, $stockMovementJson);
        $jsonStockMovement = $this->serializer->serialize($stockMovement, 'json');

        return new JsonResponse($jsonStockMovement, JsonResponse::HTTP_CREATED, [], true);
    }

    #[Route('/{id}', methods: ['PUT'])]
    public function edit(int $id, Request $request): JsonResponse
    {
        $stockMovementJson = $request->getContent();
        $stockMovement = $this->stockMovementService->edit($id, $stockMovementJson);
        $jsonStockMovement = $this->serializer->serialize($stockMovement, 'json');

        return new JsonResponse($jsonStockMovement, JsonResponse::HTTP_OK, [], true);
    }

    #[Route('/{id}', methods: ['DELETE'])]
    public function deleteById(int $id): JsonResponse
    {
        $this->stockMovementService->deleteById($id);

        return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
    }

    // Controller method
    #[Route('/bywarehouseid/{warehouseId}', name: 'app_stockmovements_bywarehouseid')]
    public function getStocksByWarehouseId($warehouseId)
    {
        $stockMovements = $this->stockMovementService->getStockMovementsByWarehouseId($warehouseId);
        return $this->json($stockMovements);
    }

    #[Route('/incoming/{id}', name: 'incoming_stock_movements', methods: ['GET'])]
    public function getIncomingStockMovementsAction($id) : Response
    {
        return $this->json($this->stockMovementService->getIncomingStockMovementsByDestinationId($id));
    }


}
?>