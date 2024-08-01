<?php

namespace App\Service;

use App\Entity\ReceptionBonCommande;
use App\Entity\DetailsBonCommande;
use App\Entity\StockMovement;
use App\Service\StockMovementService;
use App\Service\WareHouseService;
use App\Repository\ReceptionBonCommandeRepository;
use App\Service\DetailsBonCommandeService;
 

class ReceptionBonCommandeService
{
    private WareHouseService $wareHouseService;
    private ReceptionBonCommandeRepository $receptionBonCommandeRepository;
    private ProductService $produitService;
    private StockMovementService $stockMovementService;
    private TiersService $tiersService;
    private DetailsBonCommandeService $detailsBonCommandeService;
    private UnitService $unitService;
    

    public function __construct(ReceptionBonCommandeRepository $receptionBonCommandeRepository, StockMovementService $stockMovementService,  ProductService $produitService, TiersService $tiersService, WareHouseService $wareHouseService, DetailsBonCommandeService $detailsBonCommandeService, UnitService $unitService)
    {
        $this->receptionBonCommandeRepository = $receptionBonCommandeRepository;
        $this->stockMovementService = $stockMovementService;
        $this->produitService = $produitService;
        $this->tiersService = $tiersService;
        $this->wareHouseService = $wareHouseService;
        $this->detailsBonCommandeService = $detailsBonCommandeService;
        $this->unitService = $unitService;
    }

    public function save(ReceptionBonCommande $rBonCommand=null, $rBonCommandJson=null): ReceptionBonCommande
    {
        if($rBonCommand){
            return $this->receptionBonCommandeRepository->save($rBonCommand);
        }

        $rBonCommand = new ReceptionBonCommande();

        $jsonData = json_decode($rBonCommandJson, true);
        $rBonCommand->setQuantity($jsonData['quantity']) ;
        
        $rBonCommand->setDate(new \DateTime($jsonData['date']));
        $detailsBonCommande = new DetailsBonCommande();
        $detailsBonCommande = $this->detailsBonCommandeService->getById($jsonData['detailsBonCommande']['id']);
        
        $rBonCommand->setProduct($this->produitService->getById($detailsBonCommande->getProduit()->getId()));
        $rBonCommand->setDetailsBonCommande($detailsBonCommande);
        $detailsBonCommande->setRemainingNotArrivedStock($detailsBonCommande->getRemainingNotArrivedStock() - $rBonCommand->getQuantity());
        $detailsBonCommande = $this->detailsBonCommandeService->edit($detailsBonCommande->getId(), $detailsBonCommande);

        $sellUnit = $this->unitService->getByProductId($detailsBonCommande->getProduit()->getId());
        $stockQuantity = $rBonCommand->getQuantity() * $sellUnit->getConversionRate();

        $stockMovement = new StockMovement();
        $stockMovement->setOrigin(null);
        $stockMovement->setDestination($this->wareHouseService->getById($jsonData['warehouseDestination']['id']));
        $stockMovement->setQuantity($stockQuantity);
        $stockMovement->setProduct($rBonCommand->getProduct());
        $stockMovement->setReceptionBonCommande($rBonCommand);
        $stockMovement->setDate($rBonCommand->getDate());
        $stockMovement->setRemainingNotArrivedStock($stockQuantity);
        $stockMovement->setReceptionBonCommande($rBonCommand);

        

        $this->stockMovementService->save($stockMovement);
        return $stockMovement->getReceptionBonCommande();
    }  
}
