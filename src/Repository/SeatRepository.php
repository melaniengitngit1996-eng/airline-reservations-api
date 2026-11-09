<?php

namespace App\Repository;

use App\Entity\Seat;
use App\Entity\Flight;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Seat>
 */
class SeatRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Seat::class);
    }

    public function findByFlight(Flight $flight): array
    {
        return $this->createQueryBuilder('seat')
            ->where('seat.flight = :flight')
            ->setParameter('flight', $flight)
            ->getQuery()
            ->getResult();
    }

    public function findAll(): array {
        return $this->createQueryBuilder('seat')
            ->orderBy('seat.code')
            ->getQuery()
            ->getResult();
    }
}
