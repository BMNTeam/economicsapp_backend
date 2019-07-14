<?php

namespace App\Repository;

use App\Entity\StatInfo;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method StatInfo|null find($id, $lockMode = null, $lockVersion = null)
 * @method StatInfo|null findOneBy(array $criteria, array $orderBy = null)
 * @method StatInfo[]    findAll()
 * @method StatInfo[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StatInfoRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, StatInfo::class);
    }

    // /**
    //  * @return StatInfo[] Returns an array of StatInfo objects
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
    public function findOneBySomeField($value): ?StatInfo
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
