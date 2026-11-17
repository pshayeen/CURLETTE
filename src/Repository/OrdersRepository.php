<?php

namespace App\Repository;

use App\Entity\Orders;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Orders>
 */
class OrdersRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Orders::class);
    }

    /**
     * @return Orders[] orders with the given status, newest first
     */
    public function findByStatus(string $status): array
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.status = :status')
            ->setParameter('status', $status)
            ->orderBy('o.createdAt', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * Total income: the sum of every order that was not cancelled.
     */
    public function sumRevenue(): float
    {
        return (float) $this->createQueryBuilder('o')
            ->select('COALESCE(SUM(o.total), 0)')
            ->andWhere('o.status != :cancelled')
            ->setParameter('cancelled', 'cancelled')
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }

    /**
     * Orders still waiting to be handled (placed or processing).
     */
    public function countPending(): int
    {
        return (int) $this->createQueryBuilder('o')
            ->select('COUNT(o.id)')
            ->andWhere('o.status IN (:statuses)')
            ->setParameter('statuses', ['placed', 'processing'])
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }

    /**
     * @return Orders[] the latest orders, newest first
     */
    public function findRecent(int $limit = 5): array
    {
        return $this->createQueryBuilder('o')
            ->orderBy('o.createdAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Orders[] orders matching the search text and status, newest first
     */
    public function search(?string $term, ?string $status): array
    {
        $queryBuilder = $this->createQueryBuilder('o')
            ->orderBy('o.createdAt', 'DESC');

        if ($term) {
            $queryBuilder
                ->andWhere('o.customerName LIKE :term OR o.customerEmail LIKE :term')
                ->setParameter('term', '%' . $term . '%');
        }

        if ($status) {
            $queryBuilder
                ->andWhere('o.status = :status')
                ->setParameter('status', $status);
        }

        return $queryBuilder->getQuery()->getResult();
    }



//    public function findOneBySomeField($value): ?Orders
//    {
//        return $this->createQueryBuilder('o')
//            ->andWhere('o.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
