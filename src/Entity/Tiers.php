<?php

namespace App\Entity;

use App\Repository\TiersRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TiersRepository::class)]
class Tiers
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $raisonSociale = null;

    #[ORM\Column(length: 255)]
    private ?string $address = null;

    #[ORM\Column]
    private ?int $phone = null;

    #[ORM\Column(length: 255)]
    private ?string $email = null;

    /**
     * @var Collection<int, ValueAdditionalInfo>
     */
    #[ORM\OneToMany(targetEntity: ValueAdditionalInfo::class, mappedBy: 'tiers', cascade: ['all'], orphanRemoval:true)]
    private Collection $additionalInfo;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?TiersType $type = null;

    public function __construct()
    {
        $this->additionalInfo = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRaisonSociale(): ?string
    {
        return $this->raisonSociale;
    }

    public function setRaisonSociale(string $raisonSociale): static
    {
        $this->raisonSociale = $raisonSociale;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): static
    {
        $this->address = $address;

        return $this;
    }

    public function getPhone(): ?int
    {
        return $this->phone;
    }

    public function setPhone(int $phone): static
    {
        $this->phone = $phone;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return Collection<int, ValueAdditionalInfo>
     */
    public function getAdditionalInfo(): ?Collection
    {
        
        return $this->additionalInfo;
    }

    public function addAdditionalInfo(ValueAdditionalInfo $additionalInfo): static
    {
        if (!$this->additionalInfo->contains($additionalInfo)) {
            $this->additionalInfo->add($additionalInfo);
            $additionalInfo->setTiers($this);
        }

        return $this;
    }

    public function removeAdditionalInfo(ValueAdditionalInfo $additionalInfo): static
    {
        if ($this->additionalInfo->removeElement($additionalInfo)) {
            // set the owning side to null (unless already changed)
            if ($additionalInfo->getTiers() === $this) {
                $additionalInfo->setTiers(null);
            }
        }

        return $this;
    }

    public function __toString(): string
    {
        return sprintf(
            'Tiers: [ID: %d, Raison Sociale: %s, Address: %s, Phone: %d, Email: %s, valueadd : %s]',
            $this->id,
            $this->raisonSociale,
            $this->address,
            $this->phone,
            $this->email,
            implode(', ', $this->getAdditionalInfo()->toArray())
        );
    }

    public function setAdditionalInfo(Collection $additionalInfo): static
    {
        $this->additionalInfo = $additionalInfo;
        return $this;
    }

    public function getType(): ?TiersType
    {
        return $this->type ;
    }

    public function setType(?TiersType $type): static
    {
        $this->type = $type;

        return $this;
    }
}
