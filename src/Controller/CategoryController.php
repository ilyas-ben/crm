<?php

namespace App\Controller;

use App\Service\CategoryService;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/categories')]
class CategoryController extends AbstractController
{
    private CategoryService $categoryService;

    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    #[Route('/index', name: 'app_categories')]
    public function index(Request $request): Response
    {
        return $this->render('categories/categoriesList.html.twig');
    }

    #[Route('', methods: ['GET'])]
    public function getAll(): Response
    {
        return $this->json($this->categoryService->getAll());
    }

    #[Route('/byid/{id}', methods: ['GET'])]
    public function getById(int $id): Response
    {
        return $this->json($this->categoryService->getById($id));
    }

    #[Route('/add', name: 'app_category_add_form', methods: ['GET'])]
    public function addForm(): Response
    {
        return $this->render('categories/addForm.html.twig');
    }

    #[Route('', methods: ['POST'])]
    public function add(Request $request): Response
    {
        $categoryJson = $request->getContent();
        return $this->json($this->categoryService->save(null, $categoryJson));
    }

    #[Route('/{id}', name: 'app_category_edit', methods: ['PUT'])]
    public function edit(Request $request, int $id): Response
    {
        $categoryJson = $request->getContent();
        return $this->json($this->categoryService->edit($id, $categoryJson));
    }

    #[Route('/{id}', name: 'app_category_delete', methods: ['DELETE'])]
    public function delete(int $id): Response
    {
        try {
            $this->categoryService->deleteById($id);
            return new Response(null, Response::HTTP_NO_CONTENT);
        } catch (Exception $e) {
            return new Response($e->getMessage(), Response::HTTP_NOT_FOUND);
        }
    }

}
