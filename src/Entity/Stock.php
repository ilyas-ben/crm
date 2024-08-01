<?php

namespace App\Entity;

use App\Repository\StockRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: StockRepository::class)]
class Stock
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'stocks')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Product $product = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?WareHouse $wareHouse = null;

    #[ORM\Column]
    private ?int $quantity = null;

 
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

    public function getWareHouse(): ?WareHouse
    {
        return $this->wareHouse;
    }

    public function setWareHouse(?WareHouse $wareHouse): static
    {
        $this->wareHouse = $wareHouse;

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

    public function __toString(): string
{
    return sprintf(
        'Stock [ID: %d, Product: %s, Warehouse ID: %d, Quantity: %d]',
        $this->id,
        $this->product ? $this->product->getName() : 'N/A',
        $this->wareHouse ? $this->wareHouse->getId() : 0,
        $this->quantity
    );
}

 
}
