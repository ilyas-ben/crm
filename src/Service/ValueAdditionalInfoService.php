<?php

namespace App\Service;
use App\Entity\ValueAdditionalInfo;
use App\Repository\ValueAdditionalInfoRepository;
use Symfony\Component\Serializer\SerializerInterface;

class ValueAdditionalInfoService
{
    
    private SerializerInterface $serializer;
    private ValueAdditionalInfoRepository $valueAdditionalInfoRepository;
    private AdditionalInfoFieldService $additionalInfoFieldService;

    public function __construct(
        SerializerInterface $serializer,
        ValueAdditionalInfoRepository $valueAdditionalInfoRepository,
        AdditionalInfoFieldService $additionalInfoFieldService
    ) {
        $this->serializer = $serializer;
        $this->valueAdditionalInfoRepository = $valueAdditionalInfoRepository;
        $this->additionalInfoFieldService = $additionalInfoFieldService;
    }

    public function save(ValueAdditionalInfo $valueAdditionalInfo = null, string $valueAdditionalInfoJson = null): ValueAdditionalInfo
    {
        if ($valueAdditionalInfo != null) {
            return $this->valueAdditionalInfoRepository->save($valueAdditionalInfo);
        }

        if ($valueAdditionalInfoJson != null) {
            
            $valueAdditionalInfo = new $valueAdditionalInfo();

            $data = json_decode($valueAdditionalInfoJson, true);

            $valueAdditionalInfo->setValue($data['value']);
            
            $id = $data['additionalInfoField']['id'];
            $additionalInfoField = $this->additionalInfoFieldService->getById($id);
            $valueAdditionalInfo->setAdditionalInfoField($additionalInfoField);

            return $this->valueAdditionalInfoRepository->save($valueAdditionalInfo);
        }

        throw new \InvalidArgumentException('Either $valueAdditionalInfo or $valueAdditionalInfoJson must be provided.');
    }
}

?>