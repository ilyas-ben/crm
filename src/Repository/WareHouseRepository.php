<?php

namespace App\Repository;

use App\Entity\WareHouse;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<WareHouse>
 */
class WareHouseRepository extends ServiceEntityRepository
{
    private EntityManagerInterface $em;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, WareHouse::class);
        $this->em = $this->getEntityManager();
    }

    public function save(WareHouse $warehouse): WareHouse
    {
        $this->em->persist($warehouse);
        $this->em->flush();

        return $warehouse;
    }

    public function remove(WareHouse $warehouse): void
    {
        $this->em->remove($warehouse);
        $this->em->flush();
    }
}
