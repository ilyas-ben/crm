<?php

// src/Controller/ReceptionBonCommandeController.php
namespace App\Controller;

use App\Service\ReceptionBonCommandeService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/reception-bon-commandes")]
class ReceptionBonCommandeController extends AbstractController
{
    private $receptionBonCommandeService;

    public function __construct(ReceptionBonCommandeService $receptionBonCommandeService)
    {
        $this->receptionBonCommandeService = $receptionBonCommandeService;
    }

    #[Route('', name: 'save_reception_bon_commande', methods: ['POST'])]
    public function save(Request $request): JsonResponse
    {
        $rboncmdJson = $request->getContent();
        $this->receptionBonCommandeService->save(null, $rboncmdJson);

        return new JsonResponse(['status' => 'Reception Bon Commande saved!'], JsonResponse::HTTP_CREATED);
    }
}
