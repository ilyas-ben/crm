<?php

namespace App\Controller;

use App\Entity\Unit;
use App\Service\UnitService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path:"/units")]
class UnitController extends AbstractController
{
    private $unitService;

    public function __construct(UnitService $unitService)
    {
        $this->unitService = $unitService;
    }

    #[Route(path: "/{id}" , methods:['PUT'] , name: "app_unit_edit")]
    public function edit(int $id,Request $request): Response
    {
        $data = json_decode($request->getContent(), true);

        $unit = $this->unitService->edit($id, null , $data);

        if ($unit) {
            return $this->json([
                'message' => 'Unit saved successfully',
                'id' => $unit->getId()
            ], 201);
        }

        return $this->json(['message' => 'Failed to save unit'], 400);
    }

    #[Route(path: "/byProductId/{id}" , methods:['get'] , name: "app_unit_byproductid")]
    public function getByProductId(?int $id){
        return $this->json($this->unitService->getByProductId($id));
    }
}