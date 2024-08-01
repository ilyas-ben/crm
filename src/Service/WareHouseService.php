<?php

namespace App\Service;

use App\Entity\WareHouse;
use App\Repository\WareHouseRepository;
use Exception;
use Symfony\Component\HttpFoundation\Response;

class WareHouseService
{
    private WareHouseRepository $warehouseRepository;

    public function __construct(WareHouseRepository $warehouseRepository)
    {
        $this->warehouseRepository = $warehouseRepository;
    }

    public function save(WareHouse $warehouse): WareHouse
    {
        return $this->warehouseRepository->save($warehouse);
    }

    public function getAll(): array
    {
        return $this->warehouseRepository->findAll();
    }

    public function getById(int $id): ?WareHouse
    {
        return $this->warehouseRepository->find($id);
    }

    public function edit(int $id, array $warehouseData): WareHouse
    {
        $warehouse = $this->warehouseRepository->find($id);

        if (!$warehouse) {
            throw new Exception('Warehouse not found');
        }

        $warehouse->setLabel($warehouseData['label']);
        $warehouse->setAddressLine($warehouseData['addressLine']);
        $warehouse->setCity($warehouseData['city']);

        return $this->warehouseRepository->save($warehouse);
    }

    public function deleteById(int $id): void
    {
        $warehouse = $this->warehouseRepository->find($id);

        if (!$warehouse) {
            throw new Exception('Warehouse not found');
        }

        $this->warehouseRepository->remove($warehouse);
    }
}
