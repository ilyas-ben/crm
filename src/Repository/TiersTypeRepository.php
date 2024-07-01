<?php

namespace App\Repository;

use App\Entity\TiersType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TiersType>
 */
class TiersTypeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TiersType::class);
    }

    public function save(TiersType $tiersType): TiersType
{
    $this->getEntityManager()->persist($tiersType);
    $this->getEntityManager()->flush();

    return $tiersType;
}

public function delete(TiersType $tiersType): void
{
    $this->getEntityManager()->remove($tiersType);
    $this->getEntityManager()->flush();
}



    //    /**
    //     * @return TiersType[] Returns an array of TiersType objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('t')
    //            ->andWhere('t.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('t.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?TiersType
    //    {
    //        return $this->createQueryBuilder('t')
    //            ->andWhere('t.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
