<?php

namespace App\Entity;

use App\Repository\ValueAdditionalInfoRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ValueAdditionalInfoRepository::class)]
class ValueAdditionalInfo
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $value = null;

    #[ORM\ManyToOne(cascade:['persist'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?AdditionalInfoField $additionalInfoField = null;

    #[ORM\ManyToOne(inversedBy: 'additionalInfo')]
    private ?Tiers $tiers = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue(string $value): static
    {
        $this->value = $value;

        return $this;
    }

    public function getAdditionalInfoField(): ?AdditionalInfoField
    {
        return $this->additionalInfoField;
    }

    public function setAdditionalInfoField(?AdditionalInfoField $additionalInfoField): static
    {
        $this->additionalInfoField = $additionalInfoField;

        return $this;
    }

    public function getTiersid(): ?int
    {
        return $this->tiers->getId();
    }

    public function setTiers(?Tiers $tiers): static
    {
        $this->tiers = $tiers;

        return $this;
    }

    public function __toString(): string
{
    return sprintf(
        'ValueAdditionalInfo: [ID: %d, Value: %s, AdditionalInfoField: %s, Tiers ID: %d]',
        $this->id,
        $this->value,
        $this->additionalInfoField ? $this->additionalInfoField->getId() : 'null',
        $this->tiers ? $this->tiers->getId() : 'null'
    );
}

}
