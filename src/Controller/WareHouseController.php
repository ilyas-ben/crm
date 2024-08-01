<?php

namespace App\Controller;

use App\Entity\WareHouse;
use App\Service\WareHouseService;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/warehouses')]
class WareHouseController extends AbstractController
{
    private WareHouseService $warehouseService;

    public function __construct(WareHouseService $warehouseService)
    {
        $this->warehouseService = $warehouseService;
    }

    #[Route('/index', name: 'app_warehouses')]
    public function index(Request $request): Response
    {
        return $this->render('warehouses/warehousesList.html.twig');
    }

    #[Route('', methods: ['GET'])]
    public function getAll(): Response
    {
        return $this->json($this->warehouseService->getAll());
    }

    #[Route('/byid/{id}', methods: ['GET'])]
    public function getById(int $id): Response
    {
        return $this->json($this->warehouseService->getById($id));
    }

    #[Route('/add', name: 'app_warehouse_add_form', methods: ['GET'])]
    public function addForm(): Response
    {
        return $this->render('warehouses/addForm.html.twig');
    }

    #[Route('', methods: ['POST'])]
    public function add(Request $request): Response
    {
        $warehouseData = json_decode($request->getContent(), true);
        $warehouse = new WareHouse();
        $warehouse->setLabel($warehouseData['label']);
        $warehouse->setAddressLine($warehouseData['addressLine']);
        $warehouse->setCity($warehouseData['city']);
        return $this->json($this->warehouseService->save($warehouse));
    }

    #[Route('/{id}', name: 'app_warehouse_edit', methods: ['PUT'])]
    public function edit(Request $request, int $id): Response
    {
        $warehouseData = json_decode($request->getContent(), true);
        return $this->json($this->warehouseService->edit($id, $warehouseData));
    }

    #[Route('/{id}', name: 'app_warehouse_delete', methods: ['DELETE'])]
    public function delete(int $id): Response
    {
        try {
            $this->warehouseService->deleteById($id);
            return new Response(null, Response::HTTP_NO_CONTENT);
        } catch (Exception $e) {
            return new Response($e->getMessage(), Response::HTTP_NOT_FOUND);
        }
    }

    #[Route("/{id}/menu/ ", name : 'app_warehouse_menu', methods:['GET'])]
    public function displayMenu(int $id) : Response
    {
        return $this->render('warehouses/warehouseMenu.html.twig', ["warehouse"=> $this->warehouseService->getById($id) ]);
    }

    #[Route("/{id}/stocks", name:"app_warehouse_stocks", methods: ["GET"])]
    public function displayStocksOfWarehouseId(int $id) : Response
    {
        return $this->render("stocks/stocksList.html.twig", [
            "id" => $id,
            "name" => $this->warehouseService->getById($id)->getLabel()
        ]);
    }

    #[Route("/{id}/receptions", name:"app_warehouse_receptions", methods: ["GET"])]
    public function displayReceptions(int $id) : Response
    {
        return $this->render("receptions/receptionsList.html.twig", [
            "id" => $id,
            "name" => $this->warehouseService->getById($id)->getLabel()
        ]);
    }

}
