<?php

namespace App\Repository;

use App\Entity\AdditionalInfoField;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<AdditionalInfoField>
 */
class AdditionalInfoFieldRepository extends ServiceEntityRepository
{

    private EntityManagerInterface $em ;

    


    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AdditionalInfoField::class);
    }

    public function save(AdditionalInfoField $field): AdditionalInfoField
    {
        $this->em = $this->getEntityManager();
        $this->em->persist($field);
        $this->em->flush();

        return $field;
    }

    public function deleteById(int $fieldId): void
    {
        $this->em = $this->getEntityManager();
        $field = $this->find($fieldId);
        if ($field) {
            $this->em->remove($field);
            $this->em->flush();
        }
    }


    //    /**
    //     * @return AdditionalInfoField[] Returns an array of AdditionalInfoField objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('a.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?AdditionalInfoField
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
