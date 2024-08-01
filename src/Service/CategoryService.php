<?php

namespace App\Service;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use Exception;
use Symfony\Component\Serializer\SerializerInterface;

class CategoryService
{
    private CategoryRepository $categoryRepository;
    private SerializerInterface $serializer;

    public function __construct(CategoryRepository $categoryRepository, SerializerInterface $serializer)
    {
        $this->categoryRepository = $categoryRepository;
        $this->serializer = $serializer;
    }

    public function save(Category $category = null, string $categoryJson = null): Category
    {
        if ($category != null) {
            return $this->categoryRepository->save($category);
        }

        $categoryData = json_decode($categoryJson, true);

        // Create a new Category entity
        $category = new Category();
        $category->setName($categoryData['name']);

        return $this->categoryRepository->save($category);
    }

    public function getAll()
    {
        return $this->categoryRepository->findAll();
    }

    public function getById($id): Category
    {
        return $this->categoryRepository->find($id);
    }

    public function edit($id, string $newCategoryJson = null): Category
    {
        $categoryData = json_decode($newCategoryJson, true);

        $oldCategory = $this->categoryRepository->find($id);

        if (!$oldCategory) {
            throw new Exception('Category not found');
        }

        $oldCategory->setName($categoryData['name']);

        $this->categoryRepository->save($oldCategory);
        return $this->getById($id);
    }

    public function deleteById(int $id): void
{
    $category = $this->categoryRepository->find($id);

    if (!$category) {
        throw new Exception('Category not found');
    }

    $this->categoryRepository->remove($category);
}

}
