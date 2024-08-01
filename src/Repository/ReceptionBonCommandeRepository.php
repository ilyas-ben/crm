<?php

namespace App\Repository;

use App\Entity\ReceptionBonCommande;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ReceptionBonCommande>
 */
class ReceptionBonCommandeRepository extends ServiceEntityRepository
{

    private  EntityManagerInterface $em;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ReceptionBonCommande::class);
        $this->em = $this->getEntityManager();
    }

    public function save(ReceptionBonCommande $receptionBonCommande) : ReceptionBonCommande
    {
        $this->em->persist($receptionBonCommande);
        $this->em->flush();
        return $receptionBonCommande;
    }


    //    /**
    //     * @return ReceptionBonCommande[] Returns an array of ReceptionBonCommande objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('r.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?ReceptionBonCommande
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
