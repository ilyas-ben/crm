<?php
namespace App\Service;

use App\Entity\Unit;
use App\Repository\UnitRepository;


class UnitService 
{
    private UnitRepository $unitRepository;
    private ProductService $productService;

    public function __construct(UnitRepository $unitRepository , ProductService $productService)
    {
        $this->unitRepository = $unitRepository;
        $this->productService = $productService;
    }

    public function getById(int $id): ?Unit
    {
        return $this->unitRepository->find($id);
    }

    public function getAll(): array
    {
        return $this->unitRepository->findAll();
    }

    public function getByProductId(int $productId): ?Unit
    {
        return $this->unitRepository->findByProductId($productId);
    }

    public function save(Unit $unit=null, $unitJson=null) : ?Unit
    {
        if($unit)
        {return $this->save($unit);}

        $unit = new Unit();

        $unit->setProduct($this->productService->getById($unitJson['product']['id']));
        $unit->setConversionRate($unitJson['conversionRate']);
        $unit->setName(null);

        return $this->unitRepository->save($unit);
    }
    public function edit(?int $id, Unit $unit=null, $unitJson=null) : ?Unit
    {
        if($unit)
        {return $this->save($unit);}

        $unit = $this->getById($id);

        $unit->setConversionRate($unitJson['conversionRate']);

        return $this->unitRepository->save($unit);
    }
}
