<?php

namespace App\Controller;

use App\Entity\Product;
use App\Service\ProductService;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/products')]
class ProductController extends AbstractController
{
    private ProductService $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    #[Route('/index', name: 'app_products')]
    public function index(Request $request): Response
    {
        return $this->render('products/productsList.html.twig');
    }

    #[Route('', methods: ['GET'])]
    public function getAll(): Response
    {
        return $this->json($this->productService->getAll());
    }

    #[Route('/byid/{id}', methods: ['GET'])]
    public function getById(int $id): Response
    {
        return $this->json($this->productService->getById($id));
    }

    #[Route('/add', name: 'app_product_add_form', methods: ['GET'])]
    public function addForm(): Response
    {
        return $this->render('products/addForm.html.twig');
    }

    #[Route('', name: 'create_product',methods: ['POST'])]
    public function add(Request $request): Response
    {
        $productJson = $request->getContent();
        return $this->json($this->productService->save(null, $productJson));
    }

    #[Route('/{id}', name: 'app_product_edit', methods: ['PUT'])]
    public function edit(Request $request, int $id): Response
    {
        $productJson = $request->getContent();
        return $this->json($this->productService->edit($id, $productJson));
    }

    #[Route('/{id}', name: 'app_product_delete', methods: ['DELETE'])]
    public function delete(int $id): Response
    {
        try {
            $this->productService->deleteById($id);
            return new Response(null, Response::HTTP_NO_CONTENT);
        } catch (Exception $e) {
            return new Response($e->getMessage(), Response::HTTP_NOT_FOUND);
        }
    }

    private function deserializeProduct(string $productJson): Product
    {
        // Assuming you have a serializer configured to deserialize JSON to Product object
        return $this->get('serializer')->deserialize($productJson, Product::class, 'json');
    }
}
