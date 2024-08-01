<?php

namespace App\Service;

use App\Entity\Product;
use App\Entity\Unit;
use App\Repository\UnitRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Exception;

class ProductService
{
    private ProductRepository $productRepository;
    private CategoryService $categoryService;
    private UnitRepository $unitRepoUnitRepository;

    public function __construct(ProductRepository $productRepository, CategoryService $categoryService, UnitRepository $unitRepoUnitRepository)
    {
        $this->productRepository = $productRepository;
        $this->categoryService = $categoryService;
        $this->unitRepoUnitRepository = $unitRepoUnitRepository;
    }

    public function save(Product $product = null, string $productJson = null): Product
    {
        if ($product != null) {
            return $this->productRepository->save($product);
        }

        $productData = json_decode($productJson, true);

        // Create a new Product entity
        $product = new Product();
        $product->setName($productData['name']);
        $product->setPrice($productData['price']);
        $product->setDescription($productData['description']);

        // Set category after getting it by ID from service
        $category = $this->categoryService->getById($productData['category']['id']);
        $product->setCategory($category);

        $unit = new Unit();
        $unit->setProduct($this->productRepository->save($product));
        $this->unitRepoUnitRepository->save($unit);

        return $product;
    }


    public function getAll(): array
    {
        return $this->productRepository->findAll();
    }

    public function getById(int $id): ?Product
    {
        return $this->productRepository->find($id);
    }

    public function edit(int $id, string $productJson): Product
    {
        $productData = json_decode($productJson, true);

        $product = $this->getById($id);

        if (!$product) {
            throw new Exception('Product not found');
        }

        $product->setName($productData['name']);
        $product->setPrice($productData['price']);
        $product->setDescription($productData['description']);
        // Assuming $productData['category'] is the ID of the category
        $category = $this->categoryService->getById($productData['category']['id']);
        $product->setCategory($category);

        $this->productRepository->save($product);

        return $product;
    }

    public function deleteById(int $id): void
    {
        $product = $this->productRepository->find($id);

        if (!$product) {
            throw new Exception('Product not found');
        }

        $this->entityManager->remove($product);
        $this->entityManager->flush();
    }
}
