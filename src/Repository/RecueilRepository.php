<?php

namespace App\Repository;

use App\Entity\Recueil;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Recueil|null find($id, $lockMode = null, $lockVersion = null)
 * @method Recueil|null findOneBy(array $criteria, array $orderBy = null)
 * @method Recueil[]    findAll()
 * @method Recueil[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RecueilRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Recueil::class);
    }

    // /**
    //  * @return Recueil[] Returns an array of Recueil objects
    //  */



    public function getRecueilsGroupByUser($user)
    {
        $qb = $this->createQueryBuilder('r');


        $qb ->andWhere('r.utilisateur = :utilisateur')
            ->setParameter('utilisateur', $user->getId());

        return  $qb->getQuery()->getResult();

    }


    public function findByQuery($user,$startdate,$enddate,$sources,$categories)
    {
        $qb = $this->createQueryBuilder('r');

        $qb ->andWhere('r.utilisateur = :utilisateur')
            ->setParameter('utilisateur', $user->getId());

        if (sizeof($sources) != 0) {
            $qb ->andWhere('r.source IN (:sources)')
                ->setParameter('sources', $sources);
        }

        if (sizeof($categories) != 0) {
            $qb->andWhere('r.categorie IN (:categories)')
                ->setParameter('categories', $categories);
        }

        if ($enddate != "" && $startdate != ""){
            $qb->andWhere('r.createdAt BETWEEN :startdate AND :enddate')
                ->setParameter('startdate', $startdate)
                ->setParameter('enddate', $enddate );
        }

        $qb->orderBy('r.id', 'DESC') ;


        return  $qb->getQuery()->getResult();

    }
}
