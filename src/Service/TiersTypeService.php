<?php

namespace App\Service;

use App\Entity\TiersType;
use App\Repository\TiersTypeRepository;
use Symfony\Component\Serializer\SerializerInterface;
use Exception;

class TiersTypeService
{
    private TiersTypeRepository $tiersTypeRepository;
    private SerializerInterface $serializer;

    public function __construct(TiersTypeRepository $tiersTypeRepository, SerializerInterface $serializer)
    {
        $this->tiersTypeRepository = $tiersTypeRepository;
        $this->serializer = $serializer;
    }

    public function save(TiersType $tiersType = null, string $tiersTypeJson = null): TiersType
    {
        if ($tiersType != null) {
            return $this->tiersTypeRepository->save($tiersType);
        }

        $tiersTypeData = json_decode($tiersTypeJson, true);

        // Create a new TiersType entity
        $tiersType = new TiersType();
        $tiersType->setNameType($tiersTypeData['nameType']);

        return $this->tiersTypeRepository->save($tiersType);
    }

    public function getAll()
    {
        return $this->tiersTypeRepository->findAll();
    }

    public function getById($id): TiersType
    {
        return $this->tiersTypeRepository->find($id);
    }

    public function edit($id, string $newTiersTypeJson = null): TiersType
    {
        $tiersTypeData = json_decode($newTiersTypeJson, true);

        $tiersType = $this->tiersTypeRepository->find($id);

        if (!$tiersType) {
            throw new Exception('TiersType not found');
        }

        $tiersType->setNameType($tiersTypeData['nameType']);

        return $this->tiersTypeRepository->save($tiersType);
    }

    public function deleteById($id): void
    {
        $tierType = $this->tiersTypeRepository->find($id);
        if ($tierType) {
            $this->tiersTypeRepository->delete($tierType);
        } else {
            throw new \Exception('Tier Type non trouvé.');
        }
    }

}
