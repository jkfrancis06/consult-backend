<?php

namespace App\Repository;

use App\Entity\MailUser;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method MailUser|null find($id, $lockMode = null, $lockVersion = null)
 * @method MailUser|null findOneBy(array $criteria, array $orderBy = null)
 * @method MailUser[]    findAll()
 * @method MailUser[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MailUserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MailUser::class);
    }

    // /**
    //  * @return MailUser[] Returns an array of MailUser objects
    //  */

    public function findByExampleField($type)
    {
        return $this->createQueryBuilder('m')
            ->andWhere('(:val) IN m.type')
            ->setParameter('val', $type)
            ->orderBy('m.id', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }


    /*
    public function findOneBySomeField($value): ?MailUser
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
