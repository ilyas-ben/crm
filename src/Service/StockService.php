<?php

namespace App\Service;

use App\Entity\Stock;
use App\Entity\StockMovement;
use App\Repository\StockRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Proxies\__CG__\App\Entity\WareHouse;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class StockService
{
    private StockRepository $stockRepository;
    private ProductService $productService;
    private WareHouseService $wareHouseService;
    private StockMovementService $stockMovementService;

    public function __construct(StockRepository $stockRepository, ProductService $productService, WareHouseService $wareHouseService, StockMovementService $stockMovementService)
    {
        $this->stockRepository = $stockRepository;
        $this->productService = $productService;
        $this->wareHouseService = $wareHouseService;
        $this->stockMovementService = $stockMovementService;

    }

    public function save(Stock $stock = null, ?string $stockJson = null): Stock
    {
        if ($stock != null) {
            return $this->stockRepository->save($stock);
        }

        $stockData = json_decode($stockJson, true);

        // Create a new Stock entity
        $stock = new Stock();
        $stock->setProduct($this->productService->getById($stockData['product']['id']));
        $stock->setQuantity($stockData['quantity']);
        $stock->setWareHouse($this->wareHouseService->getById($stockData['wareHouse']['id']));

        return $this->stockRepository->save($stock);
    }

    public function getById(int $id): ?Stock
    {
        return $this->stockRepository->find($id);
    }

    public function edit(int $id, ?Stock $newStock = null, ?string $stockJson = null): Stock
    {
        $oldStock = new Stock();
        $oldStock = $this->stockRepository->find($id);

        if (!$oldStock) {
            throw new Exception('Stock not found');
        }

        if ($newStock) {
            $oldStock->setQuantity($newStock->getQuantity());
            $oldStock->setProduct($newStock->getProduct());
            $oldStock->setWareHouse($newStock->getWareHouse());

            $this->stockRepository->save($oldStock);
            return $oldStock;
        }

        $stockData = json_decode($stockJson, true);


        $oldStock->setQuantity($stockData['quantity']);
        $oldStock->setProduct($this->productService->getById($stockData['product']['id']));
        $oldStock->setWareHouse($this->wareHouseService->getById($stockData['wareHouse']['id']));

        $this->stockRepository->save($oldStock);

        return $oldStock;
    }

    public function deleteById(int $id): void
    {
        $stock = $this->getById($id);
        if (!$stock) {
            throw new NotFoundHttpException("The object you request does not exist ");
        }
        $this->stockRepository->delete($stock);
    }

    public function getAll(): array
    {
        return $this->stockRepository->findAll();
    }

    public function getByWarehouseIdAndProductId(int $warehouseId, int $productId): array|Stock
    {
        return $this->stockRepository->getByWarehouseIdAndProductId($warehouseId, $productId);
    }

    public function findStockByWarehouseId($warehouseId)
    {
        return $this->stockRepository->findAllByWarehouseId($warehouseId);
    }


    public function transferStock(int $stockId, int $quantityToTransfer, int $wareHouseDestinationId): ?array
    {

        if ($quantityToTransfer < 0) {
            throw new Exception('Quantity cannot be <0 !');
        } else if ($quantityToTransfer == 0) {
            throw new Exception('Quantity cannot be 0 !');
        }

        $stock = $this->getById($stockId);

        if (!$stock)
            throw new NotFoundHttpException("The stock doesnt exits ! ");

        $originWareHouse = $this->wareHouseService->getById($stock->getWareHouse()->getId());
        $destination = $this->wareHouseService->getById($wareHouseDestinationId);

        if (($quantityToTransfer > $stock->getQuantity()))
            throw new BadRequestHttpException("Not enough stock to transfer . \n " . $quantityToTransfer . "> " . $stock->getQuantity() . "in destination warehouse");

        $stock->setQuantity($stock->getQuantity() - $quantityToTransfer);



        if ($stock->getQuantity() <= 0) {
            $this->deleteById($stock->getId());

        } else {
            $this->edit($stock->getId(), $stock);
        }

        $stockMovement = new StockMovement();
        $stockMovement->setQuantity($quantityToTransfer);
        $stockMovement->setProduct($stock->getProduct());
        $stockMovement->setDate(new DateTime());
        $stockMovement->setDestination($destination);
        $stockMovement->setOrigin($originWareHouse);
        $stockMovement->setRemainingNotArrivedStock($quantityToTransfer);
        $stockMovement->setFinished(false);

        $this->stockMovementService->save($stockMovement);

        return ["origin stock" => $this->getById($stockId), "stockmovement" => $stockMovement];
        ;
    }
}
