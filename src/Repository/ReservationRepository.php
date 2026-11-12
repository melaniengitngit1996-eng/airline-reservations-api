<?php

namespace App\Repository;

use App\Entity\Reservation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Reservation>
 */
class ReservationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Reservation::class);
    }

    public function findByFlightId(?int $flightId): array
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.flight = :flightId')
            ->setParameter('flightId', $flightId)
            ->getQuery()
            ->getResult();
    }
}
