<?php

namespace App\Controller;

use App\Service\BonCommandeService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/bon-commandes')]
class BonCommandeController extends AbstractController
{
    private BonCommandeService $bonCommandeService;

    public function __construct(BonCommandeService $bonCommandeService)
    {
        $this->bonCommandeService = $bonCommandeService;
    }

    #[Route('/index', name: 'app_bon_commande')]
    public function index(): Response
    {
        return $this->render('bon_commandes/bon_commandes.html.twig');
    }


    #[Route('/addPage', name: 'app_bon_commande_addPage', methods: ['get'])]
    public function addPage() : Response
    {
        return $this->render('bon_commandes/add_form.html.twig');
    }

    #[Route('', name: 'create_bon_commande', methods: ['POST'])]
    public function createBonCommande(Request $request): Response
    {
        $bonCommandeJson = $request->getContent();
        $bonCommande = $this->bonCommandeService->save(null, $bonCommandeJson);

        return $this->json($bonCommande, 201);
    }

    #[Route('', name: 'get_all_bon_commandes', methods: ['GET'])]
    public function getAllBonCommandes(): Response
    {
        $bonCommandes = $this->bonCommandeService->getAll();
        return $this->json($bonCommandes);
    }

    #[Route('/byid/{id}', name: 'get_bon_commande', methods: ['GET'])]
    public function getBonCommande($id): Response
    {
        $bonCommande = $this->bonCommandeService->getById($id);
        return $this->json($bonCommande);
    }

    #[Route('/{id}', name: 'update_bon_commande', methods: ['PUT'])]
    public function updateBonCommande($id, Request $request): Response
    {
        $bonCommandeJson = $request->getContent();
        $bonCommande = $this->bonCommandeService->save($this->bonCommandeService->getById($id), $bonCommandeJson);

        return $this->json(['id' => $bonCommande->getId()]);
    }
}
