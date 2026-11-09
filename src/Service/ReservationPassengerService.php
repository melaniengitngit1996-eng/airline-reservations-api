<?php

namespace App\Service;

use App\Entity\ReservationPassenger;
use App\Repository\PassengerRepository;
use App\Repository\ReservationPassengerRepository;
use App\Repository\ReservationRepository;
use App\Repository\SeatRepository;
use Doctrine\ORM\EntityManagerInterface;

class ReservationPassengerService implements ReservationPassengerServiceInterface {
    public function __construct(
        private ReservationPassengerRepository $reservationPassengerService,
        private ReservationRepository $reservationService,
        private PassengerRepository $passengerService,
        private SeatRepository $seatService,
        private EntityManagerInterface $entityManager
    ) {
    }

    public function create(int $reservationId, array $data): ReservationPassenger
    {
        $reservationPassenger = new ReservationPassenger();

        $reservation = $this->reservationService->find($reservationId);

        if (empty($reservation)) {
            throw new \DomainException('Reservation not found.');
        }

        $passenger = $this->passengerService->find($data['passenger_id']);

        if (empty($passenger)) {
            throw new \DomainException('Passenger not found.');
        }

        
        $existing = $this->reservationPassengerService->findOneBy([
            'reservation' => $reservation,
            'passenger' => $passenger,
        ]);

        if ($existing !== null) {
            throw new \DomainException('Passenger is already added to this reservation.');
        }

        $seat = $this->seatService->findOneBy([
            'id' => $data['seat_id'],
            'flight' => $reservation->getFlight(),
        ]);

        if (empty($seat)) {
            throw new \DomainException('Seat not found for this flight.');
        }

        $existingReservationPassenger = $this->reservationPassengerService->findBySeat($seat);

        if ($existingReservationPassenger) {
            throw new \DomainException('Seat is already taken.');
        }

        $reservationPassenger = new ReservationPassenger();

        $reservationPassenger->setPassenger($passenger);
        $reservationPassenger->setReservation($reservation);
        $reservationPassenger->setSeat($seat);

        $this->entityManager->persist($reservationPassenger);
        $this->entityManager->flush();

        return $reservationPassenger;
    }

    public function findAll(int $reservationId): array
    {
        $reservation = $this->reservationService->find($reservationId);

        if (empty($reservation)) {
            throw new \DomainException('Reservation not found.');
        }

        return $this->reservationPassengerService->findByReservation($reservation);
    }

    public function update(int $reservationId, int $id, array $data): ?ReservationPassenger
    {
        $reservation = $this->reservationService->find($reservationId);

        if (empty($reservation)) {
            throw new \DomainException('Reservation not found.');
        }

        $reservationPassenger = $this->reservationPassengerService->find($id);

        if (empty($reservationPassenger)) {
            throw new \DomainException('Reservation passenger not found.');
        }

        if (!isset($data['seat_id'])) {
            throw new \DomainException('Seat ID is required.');
        }

        $seat = $this->seatService->findOneBy([
            'id' => $data['seat_id'],
            'flight' => $reservation->getFlight(),
        ]);

        if (empty($seat)) {
            throw new \DomainException('Seat not found for this flight.');
        }

        $existingReservationPassenger = $this->reservationPassengerService->findBySeat($seat);

        if (
            $existingReservationPassenger
            && $existingReservationPassenger->getId() !== $reservationPassenger->getId()
        ) {
            throw new \DomainException('Seat is already taken.');
        }

        $reservationPassenger->setSeat($seat);

        $this->entityManager->flush();

        return $reservationPassenger;
    }

    public function delete(int $reservationId, int $id): bool
    {
        $reservation = $this->reservationService->find($reservationId);

        if (empty($reservation)) {
            throw new \DomainException('Reservation not found.');
        }

        $reservationPassenger = $this->reservationPassengerService->find($id);

        if (empty($reservationPassenger)) {
            throw new \DomainException('Reservation passenger not found.');
        }

        if ($reservationPassenger->getReservation()->getId() !== $reservation->getId()) {
            throw new \DomainException('Reservation passenger not found.');
        }

        $this->entityManager->remove($reservationPassenger);
        $this->entityManager->flush();

        return true;
    }
}