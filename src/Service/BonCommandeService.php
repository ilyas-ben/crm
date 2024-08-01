<?php
namespace App\Service;

use App\Entity\BonCommande;
use App\Entity\DetailsBonCommande;
use App\Repository\BonCommandeRepository;

use Symfony\Component\Serializer\SerializerInterface;

class BonCommandeService
{
    private BonCommandeRepository $bonCommandeRepository;
    private TiersService $tiersService;
    private ProductService $productService;
    private UnitService $unitService;
    private SerializerInterface $serializer;

    public function __construct(
        BonCommandeRepository $bonCommandeRepository,
        TiersService $tiersService,
        SerializerInterface $serializer,
        ProductService $productService,
        UnitService $unitService
    ) {
        $this->bonCommandeRepository = $bonCommandeRepository;
        $this->tiersService = $tiersService;
        $this->serializer = $serializer;
        $this->productService = $productService;
        $this->unitService = $unitService;
    }

    public function save(BonCommande $bonCommande = null, string $bonCommandeJson = null): BonCommande
    {
        if ($bonCommande != null) {
            return $this->bonCommandeRepository->save($bonCommande);
        }

        $bonCommandeData = json_decode($bonCommandeJson, true);

        // Create a new BonCommande entity
        $bonCommande = new BonCommande();
        $fournisseur = $this->tiersService->getById($bonCommandeData['fournisseur']['id']);
        $bonCommande->setOrderNumber(hexdec(uniqid()));
        $bonCommande->setFournisseur($fournisseur);
        $bonCommande->setOrderDate(new \DateTime($bonCommandeData['orderDate']));
        $bonCommande->setFullyReceived($bonCommandeData['isFullyReceived'] ?? null);
        $bonCommande->setNotes($bonCommandeData['notes'] ?? null);
        $bonCommande->setTotalAmount($bonCommandeData['totalAmount'] ?? 0.0);
        $bonCommande->setFinalDeliveryDate(isset($bonCommandeData['finalDeliveryDate']) ? new \DateTime($bonCommandeData['finalDeliveryDate']) : null);


        foreach ($bonCommandeData['items'] as $itemData) {
            $item = new DetailsBonCommande();
            $item->setProduit(isset($itemData['product']['id']) ? $this->productService->getById($itemData['product']['id']) : null);
            $item->setQuantity($itemData['quantity'] ?? null);
            $item->setRemainingNotArrivedStock($itemData['quantity'] ?? null);
            $item->setLabel($itemData['label'] ?? null);
            $bonCommande->addItem($item);
        }

        return $this->bonCommandeRepository->save($bonCommande);
    }

    public function getAll()
    {
        return $this->bonCommandeRepository->findAll();
    }

    public function getById($id): BonCommande
    {
        return $this->bonCommandeRepository->find($id);
    }
}
