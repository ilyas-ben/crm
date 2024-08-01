<?php

namespace App\Service;

use App\Entity\DetailsBonCommande;
use App\Repository\DetailsBonCommandeRepository;

class DetailsBonCommandeService
{
    private DetailsBonCommandeRepository $detailsBonCommandeRepository;
    private BonCommandeService $bonCommandeService;

    public function __construct(DetailsBonCommandeRepository $detailsBonCommandeRepository, BonCommandeService $bonCommandeService)
    {
        $this->detailsBonCommandeRepository = $detailsBonCommandeRepository;
        $this->bonCommandeService = $bonCommandeService;
    }

    public function getById(int $id): ?DetailsBonCommande
    {
        return $this->detailsBonCommandeRepository->find($id);
    }

    public function edit(?int $id, DetailsBonCommande $detailsBonCommande) :DetailsBonCommande
    {
        $old = $this->getById($id);
        $old->setBonCommande($this->bonCommandeService->getById($detailsBonCommande->getBonCommande()));
        $old->setProduit($detailsBonCommande->getProduit());
        $old->setQuantity($detailsBonCommande->getQuantity());
        $old->setRemainingNotArrivedStock($detailsBonCommande->getRemainingNotArrivedStock());
        return $this->detailsBonCommandeRepository->save($detailsBonCommande);
    }
}
