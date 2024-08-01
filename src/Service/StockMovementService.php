<?php

namespace App\Service;

use App\Entity\StockMovement;
use App\Repository\StockMovementRepository;
use App\Repository\ProductRepository;
use App\Repository\WareHouseRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\SerializerInterface;
use Exception;

class StockMovementService
{
    private StockMovementRepository $stockMovementRepository;
    private ProductRepository $productRepository;
    private WareHouseRepository $wareHouseRepository;
    private SerializerInterface $serializer;

    public function __construct(
        StockMovementRepository $stockMovementRepository,
        ProductRepository $productRepository,
        WareHouseRepository $wareHouseRepository,
        SerializerInterface $serializer
    ) {
        $this->stockMovementRepository = $stockMovementRepository;
        $this->productRepository = $productRepository;
        $this->wareHouseRepository = $wareHouseRepository;
        $this->serializer = $serializer;
    }

    public function save(StockMovement $stockMovement = null, string $stockMovementJson = null): StockMovement
    {
        if ($stockMovement != null) {
            return $this->stockMovementRepository->save($stockMovement);
        }

        $stockMovementData = json_decode($stockMovementJson, true);

        $stockMovement = new StockMovement();
        $stockMovement->setQuantity($stockMovementData['quantity']);

        $product = $this->productRepository->find($stockMovementData['product']);
        if (!$product) {
            throw new Exception('Product not found for ID ' . $stockMovementData['product']);
        }
        $stockMovement->setProduct($product);

        $stockMovement->setDate(new \DateTime($stockMovementData['date']));

        $destination = $this->wareHouseRepository->find($stockMovementData['destination']);
        if (!$destination) {
            throw new Exception('Destination warehouse not found for ID ' . $stockMovementData['destination']);
        }
        $stockMovement->setDestination($destination);

        if (isset($stockMovementData['origin'])) {
            $origin = $this->wareHouseRepository->find($stockMovementData['origin']);
            if (!$origin) {
                throw new Exception('Origin warehouse not found for ID ' . $stockMovementData['origin']);
            }
            $stockMovement->setOrigin($origin);
        }

        $stockMovement->setRemainingNotArrivedStock($stockMovementData['remaining_not_arrived_stock']);

        return $this->stockMovementRepository->save($stockMovement);
    }

    public function getAll()
    {
        return $this->stockMovementRepository->findAll();
    }

    public function getById($id): StockMovement
    {
        return $this->stockMovementRepository->find($id);
    }

    public function edit($id, string $newStockMovementJson = null): StockMovement
    {
        $stockMovementData = json_decode($newStockMovementJson, true);

        $stockMovement = $this->stockMovementRepository->find($id);
        if (!$stockMovement) {
            throw new Exception('StockMovement not found');
        }

        $stockMovement->setQuantity($stockMovementData['quantity']);

        $product = $this->productRepository->find($stockMovementData['product']);
        if (!$product) {
            throw new Exception('Product not found for ID ' . $stockMovementData['product']);
        }
        $stockMovement->setProduct($product);

        $stockMovement->setDate(new \DateTime($stockMovementData['date']));

        $destination = $this->wareHouseRepository->find($stockMovementData['destination']);
        if (!$destination) {
            throw new Exception('Destination warehouse not found for ID ' . $stockMovementData['destination']);
        }
        $stockMovement->setDestination($destination);

        if (isset($stockMovementData['origin'])) {
            $origin = $this->wareHouseRepository->find($stockMovementData['origin']);
            if (!$origin) {
                throw new Exception('Origin warehouse not found for ID ' . $stockMovementData['origi']);
            }
            $stockMovement->setOrigin($origin);
        }

        $stockMovement->setRemainingNotArrivedStock($stockMovementData['remaining_not_arrived_stock']);

        return $this->stockMovementRepository->save($stockMovement);
    }

    public function deleteById(int $id): void
    {
        $stockMovement = $this->stockMovementRepository->find($id);
        if ($stockMovement) {
            $this->stockMovementRepository->delete($stockMovement);
        }
    }


    // Service method
    public function getStockMovementsByWarehouseId($warehouseId)
    {
        return $this->stockMovementRepository->getByWareHouseId($warehouseId);
    }

    public function getIncomingStockMovementsByDestinationId($id)
    {
        return $this->stockMovementRepository->getIncomingStockMovementsByDestinationId($id);
    }



}
?>