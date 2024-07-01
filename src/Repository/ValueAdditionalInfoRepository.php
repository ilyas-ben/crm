<?php

namespace App\Repository;

use App\Entity\ValueAdditionalInfo;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ValueAdditionalInfo>
 */
class ValueAdditionalInfoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ValueAdditionalInfo::class);
    }

    public function save(ValueAdditionalInfo $valueAdditionalInfo): ValueAdditionalInfo
    {
        dump($this->entityManager->persist($valueAdditionalInfo));
        $this->entityManager->flush();
        return $valueAdditionalInfo;
        
    }
    //    /**
    //     * @return ValueAdditionalInfo[] Returns an array of ValueAdditionalInfo objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('v')
    //            ->andWhere('v.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('v.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?ValueAdditionalInfo
    //    {
    //        return $this->createQueryBuilder('v')
    //            ->andWhere('v.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
