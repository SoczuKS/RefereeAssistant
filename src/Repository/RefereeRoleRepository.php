<?php

namespace App\Repository;

use App\Entity\RefereeRole;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method RefereeRole|null find($id, $lockMode = null, $lockVersion = null)
 * @method RefereeRole|null findOneBy(array $criteria, array $orderBy = null)
 * @method RefereeRole[]    findAll()
 * @method RefereeRole[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RefereeRoleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RefereeRole::class);
    }

    // /**
    //  * @return RefereeRole[] Returns an array of RefereeRole objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('r.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?RefereeRole
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
