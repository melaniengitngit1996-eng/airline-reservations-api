<?php

use App\Entity\Flight;
use App\Entity\Reservation;
use App\Repository\FlightRepository;
use App\Repository\PassengerRepository;
use App\Repository\ReservationRepository;
use App\Service\ReservationService;
use Doctrine\ORM\EntityManagerInterface;

it('creates a reservation', function () {
    $flight = new Flight();
    $passengerRepository = $this->createMock(PassengerRepository::class);
    $flightRepository = $this->createMock(FlightRepository::class);
    $reservationRepository = $this->createMock(ReservationRepository::class);
    $entityManager = $this->createMock(EntityManagerInterface::class);

    $entityManager->expects($this->once())
        ->method('persist');

    $entityManager->expects($this->once())
        ->method('flush');

    $flightRepository->expects($this->once())
        ->method('find')
        ->with(1)
        ->willReturn($flight);

    $service = new ReservationService(
        $reservationRepository, 
        $flightRepository, 
        $passengerRepository,
        $entityManager
    );
    $reservation = $service->create([
        'flight_id' => 1
    ]);

    expect($reservation)->toBeInstanceOf(Reservation::class);
});

it('gets all reservations', function() {
    $reservation = new Reservation();

    $reservationRepository = $this->createMock(ReservationRepository::class);
    $passengerRepository = $this->createMock(PassengerRepository::class);
    $flightRepository = $this->createStub(FlightRepository::class);

    $reservationRepository->expects($this->once())
        ->method('findByFlightId')
        ->with(null)
        ->willReturn([$reservation]);

    $entityManager = $this->createMock(EntityManagerInterface::class);

    $service = new ReservationService(
        $reservationRepository,
        $flightRepository,
        $passengerRepository,
        $entityManager
    );

    $reservations = $service->findAll();

    expect($reservations)->toHaveCount(1);
    expect($reservations[0])->toBeInstanceOf(Reservation::class);
});

it('gets reservations by flight id', function () {
    $reservation = new Reservation();

    $reservationRepository = $this->createMock(ReservationRepository::class);
    $passengerRepository = $this->createMock(PassengerRepository::class);
    $flightRepository = $this->createStub(FlightRepository::class);

    $reservationRepository->expects($this->once())
        ->method('findByFlightId')
        ->with(1)
        ->willReturn([$reservation]);

    $entityManager = $this->createMock(EntityManagerInterface::class);

    $service = new ReservationService(
        $reservationRepository,
        $flightRepository,
        $passengerRepository,
        $entityManager
    );

    $reservations = $service->findAll(1);

    expect($reservations)->toHaveCount(1);
    expect($reservations[0])->toBeInstanceOf(Reservation::class);
});

it('gets a reservation', function() {
    $reservation = new Reservation();

    $reservationRepository = $this->createMock(ReservationRepository::class);
    $passengerRepository = $this->createMock(PassengerRepository::class);
    $flightRepository = $this->createStub(FlightRepository::class);
    $reservationRepository->expects($this->once())
        ->method('find')
        ->willReturn($reservation);

    $entityManager = $this->createMock(EntityManagerInterface::class);

    $service = new ReservationService(
        $reservationRepository, 
        $flightRepository, 
        $passengerRepository,
        $entityManager
    );
    $reservation = $service->find(1);

    expect($reservation)->toBeInstanceOf(Reservation::class);
});

it('deletes a reservation', function() {
    $reservation = new Reservation();

    $reservationRepository = $this->createMock(ReservationRepository::class);
    $passengerRepository = $this->createMock(PassengerRepository::class);
    $flightRepository = $this->createStub(FlightRepository::class);
    $reservationRepository->expects($this->once())
        ->method('find')
        ->willReturn($reservation);

    $entityManager = $this->createMock(EntityManagerInterface::class);

    $entityManager->expects($this->once())
        ->method('remove');

    $entityManager->expects($this->once())
        ->method('flush');

    $service = new ReservationService(
        $reservationRepository, 
        $flightRepository, 
        $passengerRepository,
        $entityManager
    );
    $reservation = $service->delete(1);

    expect($reservation)->toBeTrue();
});