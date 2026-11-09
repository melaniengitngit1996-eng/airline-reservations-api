<?php

use App\Entity\Flight;
use App\Entity\Passenger;
use App\Entity\Reservation;
use App\Entity\ReservationPassenger;
use App\Entity\Seat;
use App\Repository\PassengerRepository;
use App\Repository\ReservationPassengerRepository;
use App\Repository\ReservationRepository;
use App\Repository\SeatRepository;
use App\Service\ReservationPassengerService;
use Doctrine\ORM\EntityManagerInterface;

it('adds a new passenger in a flight', function () {
    $flight = $this->createMock(Flight::class);

    $reservation = new Reservation();
    $reservation->setFlight($flight);

    $reservationRepository = $this->createMock(ReservationRepository::class);

    $reservationRepository->expects($this->once())
        ->method('find')
        ->with(1)
        ->willReturn($reservation);

    $reservationPassengerRepository = $this->createMock(
        ReservationPassengerRepository::class
    );

    $passenger = new Passenger();

    $passengerRepository = $this->createMock(PassengerRepository::class);

    $passengerRepository->expects($this->once())
        ->method('find')
        ->with(1)
        ->willReturn($passenger);

    $seat = new Seat();

    $seatRepository = $this->createMock(SeatRepository::class);

    $seatRepository->expects($this->once())
        ->method('findOneBy')
        ->with([
            'id' => 1,
            'flight' => $flight,
        ])
        ->willReturn($seat);

    $entityManager = $this->createMock(EntityManagerInterface::class);

    $entityManager->expects($this->once())
        ->method('persist');

    $entityManager->expects($this->once())
        ->method('flush');

    $service = new ReservationPassengerService(
        $reservationPassengerRepository,
        $reservationRepository,
        $passengerRepository,
        $seatRepository,
        $entityManager
    );

    $reservationPassenger = $service->create(1, [
        'passenger_id' => 1,
        'seat_id' => 1,
    ]);

    expect($reservationPassenger)
        ->toBeInstanceOf(ReservationPassenger::class);
});

it('finds all passenger in a flight', function () {
    $flight = new Flight();

    $reservation = new Reservation();
    $reservation->setFlight($flight);

    $reservationPassenger1 = new ReservationPassenger();
    $reservationPassenger2 = new ReservationPassenger();

    $reservationRepository = $this->createMock(ReservationRepository::class);

    $reservationRepository->expects($this->once())
        ->method('find')
        ->with(1)
        ->willReturn($reservation);

    $reservationPassengerRepository = $this->createMock(
        ReservationPassengerRepository::class
    );

    $reservationPassengerRepository->expects($this->once())
        ->method('findByReservation')
        ->with($reservation)
        ->willReturn([
            $reservationPassenger1,
            $reservationPassenger2,
        ]);

    $passengerRepository = $this->createStub(PassengerRepository::class);
    $seatRepository = $this->createStub(SeatRepository::class);
    $entityManager = $this->createStub(EntityManagerInterface::class);

    $service = new ReservationPassengerService(
        $reservationPassengerRepository,
        $reservationRepository,
        $passengerRepository,
        $seatRepository,
        $entityManager
    );

    $result = $service->findAll(1);

    expect($result)
        ->toHaveCount(2)
        ->and($result[0])
        ->toBe($reservationPassenger1)
        ->and($result[1])
        ->toBe($reservationPassenger2);
});

it('updates seat in a flight', function () {
    $flight = new Flight();

    $reservation = new Reservation();
    $reservation->setFlight($flight);

    $oldSeat = new Seat();
    $newSeat = new Seat();

    $reservationPassenger = new ReservationPassenger();
    $reservationPassenger->setReservation($reservation);
    $reservationPassenger->setSeat($oldSeat);

    $reservationRepository = $this->createMock(ReservationRepository::class);

    $reservationRepository->expects($this->once())
        ->method('find')
        ->with(1)
        ->willReturn($reservation);

    $reservationPassengerRepository = $this->createMock(
        ReservationPassengerRepository::class
    );

    $reservationPassengerRepository->expects($this->once())
        ->method('find')
        ->with(1)
        ->willReturn($reservationPassenger);

    $reservationPassengerRepository->expects($this->once())
        ->method('findBySeat')
        ->with($newSeat)
        ->willReturn(null);

    $seatRepository = $this->createMock(SeatRepository::class);

    $seatRepository->expects($this->once())
        ->method('findOneBy')
        ->with([
            'id' => 2,
            'flight' => $flight,
        ])
        ->willReturn($newSeat);

    $passengerRepository = $this->createStub(PassengerRepository::class);
    $entityManager = $this->createMock(EntityManagerInterface::class);

    $entityManager->expects($this->once())
        ->method('flush');

    $service = new ReservationPassengerService(
        $reservationPassengerRepository,
        $reservationRepository,
        $passengerRepository,
        $seatRepository,
        $entityManager
    );

    $result = $service->update(1, 1, [
        'seat_id' => 2,
    ]);

    expect($result)
        ->toBe($reservationPassenger);

    expect($result->getSeat())
        ->toBe($newSeat);
});

it('deletes passenger in a flight', function () {
    $flight = new Flight();

    $reservation = new Reservation();
    $reservation->setFlight($flight);

    $reservationPassenger = new ReservationPassenger();
    $reservationPassenger->setReservation($reservation);

    $reservationRepository = $this->createMock(ReservationRepository::class);

    $reservationRepository->expects($this->once())
        ->method('find')
        ->with(1)
        ->willReturn($reservation);

    $reservationPassengerRepository = $this->createMock(
        ReservationPassengerRepository::class
    );

    $reservationPassengerRepository->expects($this->once())
        ->method('find')
        ->with(1)
        ->willReturn($reservationPassenger);

    $passengerRepository = $this->createStub(PassengerRepository::class);
    $seatRepository = $this->createStub(SeatRepository::class);

    $entityManager = $this->createMock(EntityManagerInterface::class);

    $entityManager->expects($this->once())
        ->method('remove')
        ->with($reservationPassenger);

    $entityManager->expects($this->once())
        ->method('flush');

    $service = new ReservationPassengerService(
        $reservationPassengerRepository,
        $reservationRepository,
        $passengerRepository,
        $seatRepository,
        $entityManager
    );

    $result = $service->delete(1, 1);

    expect($result)->toBeTrue();
});