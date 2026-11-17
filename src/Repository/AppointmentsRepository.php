<?php

namespace App\Repository;

use App\Entity\Appointments;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Appointments>
 */
class AppointmentsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Appointments::class);
    }

    /**
     * @return Appointments[] appointments with the given status, soonest first
     */
    public function findByStatus(string $status): array
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.status = :status')
            ->setParameter('status', $status)
            ->orderBy('a.appointmentDate', 'ASC')
            ->addOrderBy('a.appointmentTime', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * Appointments still to come: status "upcoming" and dated today or later.
     */
    public function countUpcoming(): int
    {
        return (int) $this->createQueryBuilder('a')
            ->select('COUNT(a.id)')
            ->andWhere('a.status = :status')
            ->andWhere('a.appointmentDate >= :today')
            ->setParameter('status', 'upcoming')
            ->setParameter('today', new \DateTimeImmutable('today'), Types::DATE_IMMUTABLE)
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }

    /**
     * @return Appointments[] the next upcoming appointments, soonest first
     */
    public function findUpcoming(int $limit = 5): array
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.status = :status')
            ->andWhere('a.appointmentDate >= :today')
            ->setParameter('status', 'upcoming')
            ->setParameter('today', new \DateTimeImmutable('today'), Types::DATE_IMMUTABLE)
            ->orderBy('a.appointmentDate', 'ASC')
            ->addOrderBy('a.appointmentTime', 'ASC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Appointments[] appointments matching the search text and status, latest date first
     */
    public function search(?string $term, ?string $status): array
    {
        $queryBuilder = $this->createQueryBuilder('a')
            ->orderBy('a.appointmentDate', 'DESC')
            ->addOrderBy('a.appointmentTime', 'DESC');

        if ($term) {
            $queryBuilder
                ->andWhere('a.customerName LIKE :term OR a.customerEmail LIKE :term OR a.service LIKE :term')
                ->setParameter('term', '%' . $term . '%');
        }

        if ($status) {
            $queryBuilder
                ->andWhere('a.status = :status')
                ->setParameter('status', $status);
        }

        return $queryBuilder->getQuery()->getResult();
    }

//    public function findOneBySomeField($value): ?Appointments
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
