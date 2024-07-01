<?php

namespace App\Repository;

use App\Entity\ValueAdditionalInfoClient;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ValueAdditionalInfoClient>
 */
class ValueAdditionalInfoClientRepository extends ServiceEntityRepository
{

    private EntityManagerInterface $entityManager;

    public function __construct(ManagerRegistry $registry, EntityManagerInterface $em)
    {
        parent::__construct($registry, ValueAdditionalInfoClient::class);
        $this->entityManager = $em;
    }


    

    public function save(ValueAdditionalInfoClient $valueAdditionalInfoClient): ValueAdditionalInfoClient
    {
        dump($this->entityManager->persist($valueAdditionalInfoClient));
        $this->entityManager->flush();
        return $valueAdditionalInfoClient;
        
    }

    //    /**
    //     * @return ValueAdditionalInfoClient[] Returns an array of ValueAdditionalInfoClient objects
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

    //    public function findOneBySomeField($value): ?ValueAdditionalInfoClient
    //    {
    //        return $this->createQueryBuilder('v')
    //            ->andWhere('v.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
