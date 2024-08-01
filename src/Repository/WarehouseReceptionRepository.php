<?php

namespace App\Repository;

use App\Entity\WarehouseReception;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<WarehouseReception>
 */
class WarehouseReceptionRepository extends ServiceEntityRepository
{
    private EntityManagerInterface $em;
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, WarehouseReception::class);
        $this->em = $this->getEntityManager();
    }

    public function save(WarehouseReception $reception): WarehouseReception
    {

        $this->em->persist($reception);
        $this->em->flush();

        return $reception;
    }

    public function deleteById(int $id): void
    {
        $this->em = $this->getEntityManager();
        $reception = $this->find($id);

        if ($reception !== null) {
            $this->em->remove($reception);
            $this->em->flush();
        }
    }


    /**
     * Get all receptions of a Warehouse by its id
     * @return WarehouseReception[]
     */

    public function getByDestinationId($destinationId)
    {
        return $this->createQueryBuilder('wr')
        ->join('wr.stockMovement', 'sm')
        ->andWhere('sm.destination = :destinationId')
        ->setParameter('destinationId', $destinationId)
        ->getQuery()
        ->getResult();
    }

    

}
