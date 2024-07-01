<?php

namespace App\Repository;

use App\Entity\Tiers;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Tiers>
 */
class TiersRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Tiers::class);
    }

    public function save(Tiers $tiers): ?Tiers
    {
        $entityManager = $this->getEntityManager();
        $entityManager->persist($tiers);
        $entityManager->flush();
        return $tiers;
    }



    public function deleteById(int $id): void
    {
        $entityManager = $this->getEntityManager();
        if ($tiers = $this->find($id)) {
            $entityManager->remove($tiers);
            $entityManager->flush();
        } else
            throw new Exception("Tiers not found, id " . $id);
    }

//    /**
//     * @return Tiers[] Returns an array of Tiers objects
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

//    public function findOneBySomeField($value): ?Tiers
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
