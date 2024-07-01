<?php

namespace App\Controller;

use App\Service\TiersTypeService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/tierstypes')]
class TiersTypeController extends AbstractController
{
    private TiersTypeService $tiersTypeService;

    public function __construct(TiersTypeService $tiersTypeService)
    {
        $this->tiersTypeService = $tiersTypeService;
    }

    #[Route('/index', name: 'app_tiers_type_list')]
    public function index(): Response
    {
        return $this->render('tiers_type/tiersTypesList.html.twig');
    }

    #[Route('', methods: ['GET'])]
    public function getAll(): Response
    {
        return $this->json($this->tiersTypeService->getAll());
    }

    #[Route('/add', name: 'app_tiers_type_add_form', methods: ['GET'])]
    public function addForm(): Response
    {
        return $this->render('tiers_type/addForm.html.twig');
    }

    #[Route('', methods: ['POST'])]
    public function add(Request $request): Response
    {
        $tiersTypeJson = $request->getContent();
        return $this->json($this->tiersTypeService->save(null, $tiersTypeJson));
    }

    #[Route('/byid/{id}', name: 'app_tiers_type_get_by_id', methods: ['GET'])]
    public function getById(int $id): Response
    {
        return $this->json($this->tiersTypeService->getById($id));
    }

    #[Route('/{id}', name: 'app_tiers_type_edit', methods: ['PUT'])]
    public function edit(Request $request, int $id): Response
    {
        $tiersTypeJson = $request->getContent();
        return $this->json($this->tiersTypeService->edit($id, $tiersTypeJson));
    }



    #[Route('/{id}', name: 'app_tiers_type_delete', methods: ['DELETE'])]
    public function delete($id): JsonResponse
    {
        try {
            $this->tiersTypeService->deleteById($id);
            return new JsonResponse(['message' => 'Tier Type supprimé avec succès.'], Response::HTTP_OK);
        } catch (\Exception $e) {
            return new JsonResponse(['message' => 'Erreur interne du serveur.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

}


