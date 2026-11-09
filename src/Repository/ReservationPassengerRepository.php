<?php

namespace App\Repository;

use App\Entity\Reservation;
use App\Entity\ReservationPassenger;
use App\Entity\Seat;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ReservationPassenger>
 */
class ReservationPassengerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ReservationPassenger::class);
    }

    public function findByReservation(
        Reservation $reservation
    ): array {
        return $this->createQueryBuilder('reservationPassenger')
            ->where('reservationPassenger.reservation = :reservation')
            ->setParameter('reservation', $reservation)
            ->getQuery()
            ->getResult();
    }

    public function findBySeat(Seat $seat): ?ReservationPassenger
    {
        return $this->findOneBy([
            'seat' => $seat,
        ]);
    }

    public function isTaken(Seat $seat): bool
    {
        return $this->findOneBy([
            'seat' => $seat,
        ]) !== null;
    }
}
