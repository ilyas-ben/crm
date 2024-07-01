<?php

namespace App\Service;

use App\Entity\AdditionalInfoField;
use App\Repository\AdditionalInfoFieldRepository;
use Exception;
use Symfony\Component\Serializer\SerializerInterface;

class AdditionalInfoFieldService
{
    private AdditionalInfoFieldRepository $additionalInfoFieldRepository;
    private SerializerInterface $serializer;

    public function __construct(AdditionalInfoFieldRepository $additionalInfoFieldRepository, SerializerInterface $serializer)
    {
        $this->additionalInfoFieldRepository = $additionalInfoFieldRepository;
        $this->serializer = $serializer;
    }

    public function getAll()
    {
        return $this->additionalInfoFieldRepository->findAll();
    }

    public function getById($id)
    {
        return $this->additionalInfoFieldRepository->find($id);
    }

    public function save(AdditionalInfoField $field = null, string $fieldJson = null): AdditionalInfoField
    {
        if ($field != null) {
            return $this->additionalInfoFieldRepository->save($field);
        }

        if ($fieldJson != null) {
            $field = $this->serializer->deserialize($fieldJson, AdditionalInfoField::class, 'json');
            return $this->additionalInfoFieldRepository->save($field);
        }

        throw new \InvalidArgumentException('Either $field or $fieldJson must be provided.');
    }

    public function deleteById(int $fieldId): void
    {
        $field = $this->additionalInfoFieldRepository->find($fieldId);

        if (!$field) {
            throw new \InvalidArgumentException("Field with ID $fieldId not found.");
        }

        $this->additionalInfoFieldRepository->deleteById($fieldId);
    }

    public function edit($id, AdditionalInfoField $newField = null, string $newFieldJson = null): void
    {
        $oldField = new AdditionalInfoField();
    
        if ($id) {
            $oldField = $this->additionalInfoFieldRepository->find($id);
            if (!$oldField) {
                throw new Exception('Additional Info Field not found');
            }
        }
    
        if ($newField == null) {
            $newField = $this->serializer->deserialize($newFieldJson, AdditionalInfoField::class, 'json');
        }
    
        // Update AdditionalInfoField's infos
        $oldField->setFieldName($newField->getFieldName());
    
        $this->additionalInfoFieldRepository->save($oldField);
    }
    

    
}
