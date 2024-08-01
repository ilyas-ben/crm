<?php

namespace App\Repository;

use App\Entity\Stock;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\StockMovement;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @extends ServiceEntityRepository<Stock>
 */
class StockRepository extends ServiceEntityRepository
{
    private EntityManagerInterface $em;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Stock::class);
        $this->em = $this->getEntityManager();
    }

    public function save(Stock $stock): Stock
    {
        $this->em->persist($stock);
        $this->em->flush();

        return $stock;

    }

    public function delete($stock): void
    {


        if ($stock) {
            $this->em->remove($stock);
            $this->em->flush();
        }

    }

    /**
     * Find stock by warehouse ID and product ID.
     *
     * @param int $warehouseId
     * @param int $productId
     * @return array
     */
    public function getByWarehouseIdAndProductId(int $warehouseId, int $productId): array|Stock
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.wareHouse = :warehouseId')
            ->andWhere('s.product = :productId')
            ->setParameter('warehouseId', $warehouseId)
            ->setParameter('productId', $productId)
            ->getQuery()
            ->getResult();
    }

    /**
     * Find Stocks by WareHouse Id
     * @return Stock[]
     */

    public function findAllByWarehouseId($warehouseId)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.wareHouse = :warehouseId')
            ->setParameter('warehouseId', $warehouseId)
            ->getQuery()
            ->getResult();
    }

    


}