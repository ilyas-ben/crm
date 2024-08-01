<?php

namespace App\Service;

use App\Entity\Stock;
use App\Entity\WareHouse;
use App\Entity\WarehouseReception;
use App\Repository\WarehouseReceptionRepository;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class ReceptionService
{
    private WarehouseReceptionRepository $receptionRepository;
    private WareHouseService $warehouseService;
    private StockMovementService $stockMovementService;
    private StockService $stockService;

    public function __construct(WarehouseReceptionRepository $receptionRepository, WareHouseService $warehouseService, StockMovementService $stockMovementService, StockService $stockService)
    {
        $this->receptionRepository = $receptionRepository;
        $this->warehouseService = $warehouseService;
        $this->stockMovementService = $stockMovementService;
        $this->stockService = $stockService;
    }

    public function getReceptionsByDestinationId(int $destinationId): array
    {
        return $this->receptionRepository->getByDestinationId($destinationId);
    }

    public function save(WarehouseReception $reception = null, $receptionJson = null): ?array
    {
        if (null === $reception) {
            $reception = new WarehouseReception();
            $receptionData = json_decode($receptionJson, true);
            $stockMovement = $this->stockMovementService->getById($receptionData["stockMovementId"]);
            $reception->setQuantity($receptionData['quantity']);
        } else {
            $stockMovement = $reception->getStockMovement();
        }

        if($stockMovement->isFinished()) throw new BadRequestHttpException("This stock movement is already been acheived !");
        if($reception->getQuantity() > $stockMovement->getRemainingNotArrivedStock()) throw new BadRequestHttpException("The reception quantity exceeds the stock movement remaiang quantity ! \n reception quantity : ". $reception->getQuantity(). "> Stock movement quantity : ".$stockMovement->getQuantity() );

        $reception->setStockMovement($stockMovement);
        $reception->setProduct($stockMovement->getProduct());
        $reception->setDate(new \DateTime());

        $destinationWarehouse = $stockMovement->getDestination();
        $destinationStock = $this->stockService->getByWarehouseIdAndProductId($destinationWarehouse->getId(), $reception->getProduct()->getId())[0] ?? null;

        
        $stockMovement->setRemainingNotArrivedStock($stockMovement->getRemainingNotArrivedStock() - $reception->getQuantity());
        if($stockMovement->getRemainingNotArrivedStock() === 0) $stockMovement->setFinished(true) ;

        if (!$destinationStock) {
            $destinationStock = new Stock();
            $destinationStock->setProduct($reception->getProduct());
            $destinationStock->setQuantity($reception->getQuantity());
            $destinationStock->setWareHouse($destinationWarehouse);
            $this->stockService->save($destinationStock);
        }
        else{
            $destinationStock->setQuantity($destinationStock->getQuantity() + $reception->getQuantity());
            $this->stockService->edit($destinationStock->getId(), $destinationStock);
        }

    
        return [
            'reception' => $this->receptionRepository->save($reception),
            'stockmovement' => $this->stockMovementService->getById($stockMovement->getId()),
            'destination stock' => $this->stockService->getById($destinationStock->getId()),
        ];
    }

    public function getAll(): array
    {
        return $this->receptionRepository->findAll();
    }
}
