<?php

namespace App\Controller;

use App\Service\AdditionalInfoFieldService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/additional-info-fields')]
class AdditionalInfoFieldController extends AbstractController
{
    private AdditionalInfoFieldService $additionalInfoFieldService;

    public function __construct(AdditionalInfoFieldService $additionalInfoFieldService)
    {
        $this->additionalInfoFieldService = $additionalInfoFieldService;
    }

    #[Route('/index', name: 'additional_info_fields_list', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('additional_info_fields/additionalInfoFieldsList.html.twig');
    }

    #[Route('', name: "app_additional_info_fields_getall", methods: ['GET'])]
    public function getAll(): Response
    {
        return $this->json($this->additionalInfoFieldService->getAll());
    }

    #[Route('/byid/{id}', methods: ['GET'])]
    public function getById(int $id): Response
    {
        return $this->json($this->additionalInfoFieldService->getById($id));
    }

    #[Route('/add', name: 'app_additional_info_fields_add', methods: ['GET'])]
    public function addAdditionalInfoField(Request $request): Response
    {
        return $this->render('additional_info_fields/addForm.html.twig');
    }

    #[Route('', methods: ['POST'])]
    public function add(Request $request): Response
    {
        $fieldJson = $request->getContent();
        return $this->json($this->additionalInfoFieldService->save(null, $fieldJson), Response::HTTP_CREATED);
    }

    #[Route('/{id}', methods: ['PUT'])]
    public function edit(Request $request, $id): Response
    {
        $clientJson = $request->getContent();
        return $this->json($this->additionalInfoFieldService->edit($id, null, $clientJson), Response::HTTP_OK);
    }

    #[Route('/{id}', methods: ['DELETE'])]
    public function deleteById($id): Response
    {
        $this->additionalInfoFieldService->deleteById($id);
        return new Response(200);
    }
}
