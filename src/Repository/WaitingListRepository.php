<?php

namespace App\Repository;

use App\Entity\WaitingList;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method WaitingList|null find($id, $lockMode = null, $lockVersion = null)
 * @method WaitingList|null findOneBy(array $criteria, array $orderBy = null)
 * @method WaitingList[]    findAll()
 * @method WaitingList[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class WaitingListRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, WaitingList::class);
    }

    public function countAllForTicket($ticket)
    {
        return $this->createQueryBuilder('wl')
            ->select('COUNT(wl.id)')
            ->where('wl.ticket = :ticket')
            ->setParameter('ticket', $ticket)
            ->getQuery()
            ->getSingleScalarResult();
        ;
    }

//    /**
//     * @return WaitingList[] Returns an array of WaitingList objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('w')
            ->andWhere('w.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('w.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?WaitingList
    {
        return $this->createQueryBuilder('w')
            ->andWhere('w.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
