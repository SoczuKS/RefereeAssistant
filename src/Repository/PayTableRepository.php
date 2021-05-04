<?php

namespace App\Repository;

use App\Entity\PayTable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PayTable|null find($id, $lockMode = null, $lockVersion = null)
 * @method PayTable|null findOneBy(array $criteria, array $orderBy = null)
 * @method PayTable[]    findAll()
 * @method PayTable[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PayTableRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PayTable::class);
    }

    // /**
    //  * @return PayTable[] Returns an array of PayTable objects
    //  */
    /*
    public function findByExampleField($value) {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?PayTable {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
