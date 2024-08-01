<?php

namespace App\Entity;

use App\Repository\StockMovementRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: StockMovementRepository::class)]
class StockMovement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $quantity = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Product $product = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date = null;

    #[ORM\ManyToOne(inversedBy: 'stockMovements')]
    #[ORM\JoinColumn(nullable: false)]
    private ?WareHouse $destination = null;

    #[ORM\ManyToOne]
    private ?WareHouse $origin = null;

    #[ORM\OneToOne(cascade: ['persist'])]
    private ?ReceptionBonCommande $receptionBonCommande = null;

    #[ORM\Column]
    private ?int $remainingNotArrivedStock = null;

    #[ORM\Column]
    private ?bool $isFinished = false;
    
    public function __construct()
    {
        $this->warehouseReceptions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): static
    {
        $this->product = $product;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): static
    {
        $this->date = $date;

        return $this;
    }

    public function getDestination(): ?WareHouse
    {
        return $this->destination;
    }

    public function setDestination(?WareHouse $destination): static
    {
        $this->destination = $destination;

        return $this;
    }

    public function getOrigin(): ?WareHouse
    {
        return $this->origin;
    }

    public function setOrigin(?WareHouse $origin): static
    {
        $this->origin = $origin;

        return $this;
    }

    

    public function getReceptionBonCommande(): ?ReceptionBonCommande
    {
        return $this->receptionBonCommande;
    }

    public function setReceptionBonCommande(?ReceptionBonCommande $receptionBonCommande): static
    {
        $this->receptionBonCommande = $receptionBonCommande;

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

    public function isFinished(): ?bool
    {
        return $this->isFinished;
    }

    public function setFinished(bool $isFinished): static
    {
        $this->isFinished = $isFinished;

        return $this;
    }

    public function __toString(): string
{
    return "StockMovement{id=$this->id, quantity=$this->quantity, date=" . ($this->date ? $this->date->format('Y-m-d H:i:s') : 'null') . 
           ", destination=" . ($this->destination ? $this->destination->getLabel() : 'null') . 
           ", origin=" . ($this->origin ? $this->origin->getLabel() : 'null') . 
           ", remainingNotArrivedStock=$this->remainingNotArrivedStock, isFinished=" . ($this->isFinished ? 'true' : 'false') . 
           ", receptionBonCommande=" . $this->receptionBonCommande->getQuantity() . "}";
}

}
