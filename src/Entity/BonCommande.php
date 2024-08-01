<?php

namespace App\Entity;

use App\Repository\BonCommandeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BonCommandeRepository::class)]
class BonCommande
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'bonCommandes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Tiers $fournisseur = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $orderDate = null;

    #[ORM\Column(nullable: true)]
    private ?bool $isFullyReceived = null;

    /**
     * @var Collection<int, DetailsBonCommande>
     */
    #[ORM\OneToMany(targetEntity: DetailsBonCommande::class, mappedBy: 'bonCommande', cascade: ['persist'])]
    private Collection $items;

    #[ORM\Column]
    private ?float $totalAmount = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $finalDeliveryDate = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $notes = null;

    #[ORM\Column(type: Types::BIGINT)]
    private ?string $orderNumber = null;

    

    public function __construct()
    {
        $this->items = new ArrayCollection();
        $this->receptionsBonCommande = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFournisseur(): ?Tiers
    {
        return $this->fournisseur;
    }

    public function setFournisseur(?Tiers $fournisseur): static
    {
        $this->fournisseur = $fournisseur;

        return $this;
    }

    public function getOrderDate(): ?\DateTimeInterface
    {
        return $this->orderDate;
    }

    public function setOrderDate(\DateTimeInterface $orderDate): static
    {
        $this->orderDate = $orderDate;

        return $this;
    }

    public function isFullyReceived(): ?bool
    {
        return $this->isFullyReceived;
    }

    public function setFullyReceived(?bool $isFullyReceived): static
    {
        $this->isFullyReceived = $isFullyReceived;

        return $this;
    }

    /**
     * @return Collection<int, DetailsBonCommande>
     */
    public function getItems(): Collection
    {
        return $this->items;
    }

    public function addItem(DetailsBonCommande $item): static
    {
        if (!$this->items->contains($item)) {
            $this->items->add($item);
            $item->setBonCommande($this);
        }

        return $this;
    }

    public function removeItem(DetailsBonCommande $item): static
    {
        if ($this->items->removeElement($item)) {
            // set the owning side to null (unless already changed)
            if ($item->getBonCommande() === $this) {
                $item->setBonCommande(null);
            }
        }

        return $this;
    }

    public function getTotalAmount(): ?float
    {
        return $this->totalAmount;
    }

    public function setTotalAmount(float $totalAmount): static
    {
        $this->totalAmount = $totalAmount;

        return $this;
    }

    public function getFinalDeliveryDate(): ?\DateTimeInterface
    {
        return $this->finalDeliveryDate;
    }

    public function setFinalDeliveryDate(?\DateTimeInterface $finalDeliveryDate): static
    {
        $this->finalDeliveryDate = $finalDeliveryDate;

        return $this;
    }

    public function getNotes(): ?string
    {
        return $this->notes;
    }

    public function setNotes(?string $notes): static
    {
        $this->notes = $notes;

        return $this;
    }

    public function getOrderNumber(): ?string
    {
        return $this->orderNumber;
    }

    public function setOrderNumber(string $orderNumber): static
    {
        $this->orderNumber = $orderNumber;

        return $this;
    }

}
