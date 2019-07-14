<?php

namespace App\Repository;

use App\Entity\FarmCategory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method FarmCategory|null find($id, $lockMode = null, $lockVersion = null)
 * @method FarmCategory|null findOneBy(array $criteria, array $orderBy = null)
 * @method FarmCategory[]    findAll()
 * @method FarmCategory[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FarmCategoryRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, FarmCategory::class);
    }

    // /**
    //  * @return FarmCategory[] Returns an array of FarmCategory objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('f.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?FarmCategory
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
