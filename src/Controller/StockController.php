<?php


namespace App\Controller;

use App\Service\StockService;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/stocks')]
class StockController extends AbstractController
{
    private StockService $stockService;

    public function __construct(StockService $stockService)
    {
        $this->stockService = $stockService;
    }

    #[Route('/index', name: 'app_stocks')]
    public function index(Request $request): Response
    {
        return $this->render('stocks/stocksList.html.twig');
    }

    #[Route('', methods: ['GET'])]
    public function getAll(): Response
    {
        return $this->json($this->stockService->getAll());
    }

    #[Route('/byid/{id}', methods: ['GET'])]
    public function getById(int $id): Response
    {
        return $this->json($this->stockService->getById($id));
    }



    #[Route('/add', name: 'app_stock_add_form', methods: ['GET'])]
    public function addForm(): Response
    {
        return $this->render('stocks/addForm.html.twig');
    }

    #[Route('', methods: ['POST'])]
    public function add(Request $request): Response
    {
        $stockJson = $request->getContent();
        return $this->json($this->stockService->save(null, $stockJson));
    }


    #[Route('/{id}', name: 'app_stock_edit', methods: ['PUT'])]
    public function edit(Request $request, int $id): Response
    {
        $stockJson = $request->getContent();

        return $this->json($this->stockService->edit($id, null, $stockJson));
    }

    #[Route('/{id}', name: 'app_stock_delete', methods: ['DELETE'])]
    public function delete(int $id): Response
    {
        try {
            $this->stockService->deleteById($id);
            return new Response(null, Response::HTTP_NO_CONTENT);
        } catch (Exception $e) {
            return new Response($e->getMessage(), Response::HTTP_NOT_FOUND);
        }
    }

    #[Route('/transfert', name: "app_stock_transfert", methods: ['post'])]
    public function transfertStock(Request $request): Response
    {
        $params = json_decode($request->getContent(), true);

        return $this->json($this->stockService->transferStock($params['stockId'], $params['quantityToTransfer'], $params['warehouseDestinationId']), Response::HTTP_OK);
    }

    #[Route('/bywarehouseid/{warehouseId}', name:'app_stocks_bywarehouseid', methods: ['GET'])]
    public function findStockByWarehouseIdAction($warehouseId): Response
    {
        return $this->json($this->stockService->findStockByWarehouseId($warehouseId));
    }

}

?>