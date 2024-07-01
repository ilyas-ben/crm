<?php

namespace App\Service;

use App\Entity\Tiers;
use App\Entity\ValueAdditionalInfo;
use App\Repository\TiersRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Exception;
use Symfony\Component\Serializer\SerializerInterface;


class TiersService
{
    private TiersRepository $tiersRepository;
    private SerializerInterface $serializer;
    private AdditionalInfoFieldService $additionalInfoFieldService;
    private TiersTypeService $tiersTypeService;

    public function __construct(TiersRepository $tiersRepository, SerializerInterface $serializer, AdditionalInfoFieldService $additionalInfoFieldService, TiersTypeService $tiersTypeService)
    {
        $this->tiersRepository = $tiersRepository;
        $this->serializer = $serializer;
        $this->additionalInfoFieldService = $additionalInfoFieldService;
         $this->tiersTypeService = $tiersTypeService;

    }

    public function getAll()
    {
        return $this->tiersRepository->findAll();
    }

    public function getById($id)
    {
        return $this->tiersRepository->find($id);
    }

    public function save(Tiers $tiers = null, string $tiersJson = null): Tiers
    {
        if ($tiers != null) {
            return $this->tiersRepository->save($tiers);
        }

        if ($tiersJson != null) {
            $tiers = new Tiers();
            $tiersData = json_decode($tiersJson, true);
            $tiers->setRaisonSociale($tiersData["raisonSociale"]);
            $tiers->setAddress($tiersData["address"]);
            $tiers->setEmail($tiersData["email"]);
            $tiers->setPhone($tiersData["phone"]);

            if (isset($tiersData['additionalInfos'])) {
                foreach ($tiersData['additionalInfos'] as $additionalInfoData) {
                    // Fetch AdditionalInfo entity from database by ID

                    $additionalInfo = new ValueAdditionalInfo();
                    $additionalInfo->setValue($additionalInfoData['value']);
                    $additionalInfo->setAdditionalInfoField($this->additionalInfoFieldService->getById($additionalInfoData['additionalInfoField']['id']));

                    // Associate AdditionalInfo with Tiers
                    $tiers->addAdditionalInfo($additionalInfo);
                }
            }

            $tiersType = $this->tiersTypeService->getById($tiersData["type"]["id"]);

            $tiers->setType($tiersType);

            

            return $this->tiersRepository->save($tiers);
        }




        throw new \InvalidArgumentException('Either $tiers or $tiersJson must be provided.');
    }

    public function deleteById(int $tiersId): void
    {
        $tiers = $this->tiersRepository->find($tiersId);

        if (!$tiers) {
            throw new \InvalidArgumentException("Tiers with ID $tiersId not found.");
        }

        $this->tiersRepository->deleteById($tiersId);
    }

    public function edit($id, Tiers $newTiers = null, string $newTiersJson = null): void
    {
        $oldTiers = new Tiers();

        if ($id) {
            $oldTiers = $this->tiersRepository->find($id);
            if (!$oldTiers) {
                throw new Exception('Tiers not found');
            }
        }

        if ($newTiers == null) {
            $newTiers = $this->serializer->deserialize($newTiersJson, Tiers::class, 'json');
        }
        $newTiersData = json_decode($newTiersJson, true);

        // setting Tiers's infos
        $oldTiers->setRaisonSociale($newTiers->getRaisonSociale());
        $oldTiers->setEmail($newTiers->getEmail());
        $oldTiers->setAddress($newTiers->getAddress());
        $oldTiers->setPhone($newTiers->getPhone());
        $oldTiers->setAdditionalInfo(new ArrayCollection());


        if (isset($newTiersData['additionalInfos'])) {
            $additionalInfoss = new ArrayCollection();
            foreach ($newTiersData['additionalInfos'] as $additionalInfoData) {

                // Fetch AdditionalInfo entity from database by ID
                $additionalInfo = new ValueAdditionalInfo();
                $additionalInfo->setValue($additionalInfoData['value']);
                $additionalInfo->setAdditionalInfoField($this->additionalInfoFieldService->getById($additionalInfoData['additionalInfoField']['id']));

                // Associate AdditionalInfo with Tiers
                $oldTiers->addAdditionalInfo($additionalInfo);
            }
        }

        $tiersType = $this->tiersTypeService->getById($newTiersData["type"]["id"]);

        $oldTiers->setType($tiersType);

        $this->tiersRepository->save($oldTiers);
    }


    // Dans TiersService
    public function getAdditionalInfoByTiersId(int $tiersId): array
    {
        $tiers = new Tiers();
        $tiers = $this->tiersRepository->find($tiersId);
        
        
        return $tiers ? $tiers->getAdditionalInfo()->toArray() : [];
    }

}


?>