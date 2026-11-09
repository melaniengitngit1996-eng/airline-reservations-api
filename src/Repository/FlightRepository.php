<?php

namespace App\Repository;

use App\Entity\Airport;
use App\Entity\Flight;
use DateTimeImmutable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Flight>
 */
class FlightRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Flight::class);
    }

    public function findAll(): array {
        return $this->createQueryBuilder('flight')
            ->orderBy('flight.departureTime')
            ->getQuery()
            ->getResult();
    }

    public function findByRouteAndDepartureDate(
        Airport $originAirport,
        Airport $destinationAirport,
        \DateTimeImmutable $date
    ): array {
        $startOfDay = $date->setTime(0, 0, 0);
        $endOfDay = $startOfDay->modify('+1 day');

        return $this->createQueryBuilder('flight')
            ->where('flight.originAirport = :originAirport')
            ->andWhere('flight.destinationAirport = :destinationAirport')
            ->andWhere('flight.departureTime >= :startOfDay')
            ->andWhere('flight.departureTime < :endOfDay')
            ->setParameter('originAirport', $originAirport)
            ->setParameter('destinationAirport', $destinationAirport)
            ->setParameter('startOfDay', $startOfDay)
            ->setParameter('endOfDay', $endOfDay)
            ->orderBy('flight.departureTime')
            ->getQuery()
            ->getResult();
    }

    public function findByFlightNumber(string $flightNumber): ?Flight
    {
        return $this->findOneBy([
            'flightNumber' => $flightNumber,
        ]);
    }
}
