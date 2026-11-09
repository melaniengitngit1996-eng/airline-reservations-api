<?php

namespace App\Service;

use App\Entity\Reservation;
use App\Repository\FlightRepository;
use App\Repository\PassengerRepository;
use App\Repository\ReservationRepository;
use Doctrine\ORM\EntityManagerInterface;

class ReservationService implements ReservationServiceInterface {
    public function __construct(
        private ReservationRepository $reservationRepository,
        private FlightRepository $flightRepository,
        private PassengerRepository $passengerRepository,
        private EntityManagerInterface $entityManager
    ) {
    }

    public function create(array $data): Reservation {
        $reservation = new Reservation();

        $flight = $this->flightRepository->find($data['flight_id']);

        if (!$flight) {
            throw new \DomainException('Flight not found.');
        }

        $reservation->setFlight($flight);

        $this->entityManager->persist($reservation);
        $this->entityManager->flush();

        return $reservation;
    }

    public function findAll(?int $flightId = null): array
    {
        return $this->reservationRepository->findByFlightId($flightId);
    }

    public function find(int $id): ?Reservation
    {
        return $this->reservationRepository->find($id);
    }

    public function delete(int $id): bool
    {
       $reservation = $this->reservationRepository->find($id);

       if (!$reservation) {
            throw new \DomainException('Reservation not found.');
        }

        $this->entityManager->remove($reservation);
        $this->entityManager->flush();

        return true;
    }
}