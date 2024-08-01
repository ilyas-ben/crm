<?php

namespace App\Entity;


use App\Repository\WarehouseReceptionRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
        
#[ORM\Entity(repositoryClass: WarehouseReceptionRepository::class)]
class WarehouseReception
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Product $product = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date = null;


    #[ORM\Column]
    private ?int $quantity = null;

    #[ORM\ManyToOne(inversedBy: 'warehouseReceptions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?StockMovement $stockMovement = null;

    public function getId(): ?int
    {
        return $this->id;
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


    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): static
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getStockMovement(): ?StockMovement
    {
        return $this->stockMovement;
    }

    public function setStockMovement(?StockMovement $stockMovement): static
    {
        $this->stockMovement = $stockMovement;

        return $this;
    }

    public function __toString(): string
    {
        return sprintf(
            "WarehouseReception[id=%d, product=%s, quantity=%d, date=%s, stockMovement=%s]",
            $this->id,
            $this->product ? $this->product->getName() : 'null',
            $this->quantity,
            $this->date ? $this->date->format('Y-m-d H:i:s') : 'null',
            $this->stockMovement ? $this->stockMovement->getId() : 'null'
        );
    }

}
