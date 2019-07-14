<?php

namespace App\Repository;

use App\Entity\CultureType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method CultureType|null find($id, $lockMode = null, $lockVersion = null)
 * @method CultureType|null findOneBy(array $criteria, array $orderBy = null)
 * @method CultureType[]    findAll()
 * @method CultureType[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CultureTypeRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, CultureType::class);
    }

    // /**
    //  * @return CultureType[] Returns an array of CultureType objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?CultureType
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
