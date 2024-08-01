<?php

namespace App\Repository;

use App\Entity\StockMovement;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<StockMovement>
 */
class StockMovementRepository extends ServiceEntityRepository
{

    private EntityManagerInterface $_em;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, StockMovement::class);
        $this->_em = $this->getEntityManager();
    }

    public function save(StockMovement $stockMovement): StockMovement
    {
        $this->_em->persist($stockMovement);
        $this->_em->flush();
        return $stockMovement;
    }

    public function deleteById(int $id): void
    {
        $stockMovement = $this->find($id);
        if ($stockMovement) {
            $this->_em->remove($stockMovement);
            $this->_em->flush();
        }
    }

    /**
     * 
     * @return StockMovement[]
     */

    public function getByWareHouseId($warehouseId)
    {
        return $this->createQueryBuilder('sm')
            ->andWhere('sm.origin = :warehouseId OR sm.destination = :warehouseId')
            ->setParameter('warehouseId', $warehouseId)
            ->getQuery()
            ->getResult();
    }



    //    /**
    //     * @return StockMovement[] Returns an array of StockMovement objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('s.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?StockMovement
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    /**
     * @return StockMovement[]
     */
    public function getIncomingStockMovementsByDestinationId($id)
    {
        return $this->createQueryBuilder('sm')
            ->where('sm.destination = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getResult();
    }
}
