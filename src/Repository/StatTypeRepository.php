<?php

namespace App\Repository;

use App\Entity\StatType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method StatType|null find($id, $lockMode = null, $lockVersion = null)
 * @method StatType|null findOneBy(array $criteria, array $orderBy = null)
 * @method StatType[]    findAll()
 * @method StatType[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StatTypeRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, StatType::class);
    }

    // /**
    //  * @return StatType[] Returns an array of StatType objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?StatType
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
