<?php

namespace App\Entity;

use App\Repository\DetailsBonCommandeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DetailsBonCommandeRepository::class)]
class DetailsBonCommande
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: true)]
    private ?Product $produit = null;

    #[ORM\Column]
    private ?int $quantity = null;

    

    #[ORM\ManyToOne(inversedBy: 'items')]
    private ?BonCommande $bonCommande = null;

    /**
     * @var Collection<int, ValueAdditionalInfo>
     */
    #[ORM\OneToMany(targetEntity: ValueAdditionalInfo::class, mappedBy: 'orderItem')]
    private Collection $additionalInfos;

    #[ORM\Column(nullable:true)]
    private ?int $remainingNotArrivedStock = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $label = null;

    public function __construct()
    {
        $this->remainingNotArrivedStock = $this->quantity;
        $this->additionalInfos = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProduit(): ?Product
    {
        return $this->produit;
    }

    public function setProduit(?Product $produit): static
    {
        $this->produit = $produit;

        return $this;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): static
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getBonCommande(): ?int
    {
        return $this->bonCommande->getId();
    }

    public function setBonCommande(?BonCommande $bonCommande): static
    {
        $this->bonCommande = $bonCommande;

        return $this;
    }

    /**
     * @return Collection<int, ValueAdditionalInfo>
     */
    public function getAdditionalInfos(): Collection
    {
        return $this->additionalInfos;
    }

    public function addAdditionalInfo(ValueAdditionalInfo $additionalInfo): static
    {
        if (!$this->additionalInfos->contains($additionalInfo)) {
            $this->additionalInfos->add($additionalInfo);
            $additionalInfo->setOrderItem($this);
        }

        return $this;
    }

    public function removeAdditionalInfo(ValueAdditionalInfo $additionalInfo): static
    {
        if ($this->additionalInfos->removeElement($additionalInfo)) {
            // set the owning side to null (unless already changed)
            if ($additionalInfo->getOrderItem() === $this) {
                $additionalInfo->setOrderItem(null);
            }
        }

        return $this;
    }

    public function getRemainingNotArrivedStock(): ?int
    {
        return $this->remainingNotArrivedStock;
    }

    public function setRemainingNotArrivedStock(int $remainingNotArrivedStock): static
    {
        $this->remainingNotArrivedStock = $remainingNotArrivedStock;

        return $this;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(?string $label): static
    {
        $this->label = $label;

        return $this;
    }
}
