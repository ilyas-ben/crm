<?php

namespace App\Entity;

use App\Repository\ReceptionBonCommandeRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ReceptionBonCommandeRepository::class)]
class ReceptionBonCommande
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

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?DetailsBonCommande $detailsBonCommande = null;

    /* implement a function  that has how much stock remains from bon commande */

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

    public function setQuantity(?int $quantity): static
    {
        $this->quantity = $quantity;
        return $this;
    }

    public function getDetailsBonCommande(): ?DetailsBonCommande
    {
        return $this->detailsBonCommande;
    }

    public function setDetailsBonCommande(?DetailsBonCommande $detailsBonCommande): static
    {
        $this->detailsBonCommande = $detailsBonCommande;

        return $this;
    }

   

    
}
