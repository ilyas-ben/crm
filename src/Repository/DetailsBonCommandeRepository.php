<?php

namespace App\Repository;

use App\Entity\DetailsBonCommande;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DetailsBonCommande>
 */
class DetailsBonCommandeRepository extends ServiceEntityRepository
{

    private EntityManagerInterface $em;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DetailsBonCommande::class);
        $this->em = $this->getEntityManager();
    }

    public function save(DetailsBonCommande $detailsBonCommande): DetailsBonCommande
    {
        $this->em->persist($detailsBonCommande);
        $this->em->flush();

        return $detailsBonCommande;
    }

  



    //    /**
    //     * @return DetailsBonCommande[] Returns an array of DetailsBonCommande objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('d')
    //            ->andWhere('d.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('d.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?DetailsBonCommande
    //    {
    //        return $this->createQueryBuilder('d')
    //            ->andWhere('d.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
