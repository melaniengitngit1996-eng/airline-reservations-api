<?php

use App\Controller\Reservation\GetReservationController;
use App\Entity\Airport;
use App\Entity\Flight;
use App\Entity\Passenger;
use App\Entity\Reservation;
use App\Entity\ReservationPassenger;
use App\Entity\Seat;
use App\Repository\ReservationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\ObjectMapper\ObjectMapperInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

it('has get reservation API endpoint.', function () {
    $client = $this->createClient();

    $entityManager = $this->createMock(EntityManagerInterface::class);
    $validator = $this->createMock(ValidatorInterface::class);
    $objectMapper = $this->createMock(ObjectMapperInterface::class);

    $departureAirport = new Airport();
    $arrivalAirport = new Airport();

    $flight = new Flight();
    $flight->setFlightNumber('5J 744');
    $flight->setOriginAirport($departureAirport);
    $flight->setDestinationAirport($arrivalAirport);
    $flight->setDepartureTime(
        new DateTimeImmutable('2026-11-10T06:00:00+08:00')
    );
    $flight->setArrivalTime(
        new DateTimeImmutable('2026-11-10T07:30:00+08:00')
    );

    $passenger = new Passenger();
    $passenger->setFirstName('Juan');
    $passenger->setLastName('Dela Cruz');
    $passenger->setEmail('juan@example.com');

    $seat = new Seat();
    $seat->setCode('A1');

    $reservationPassenger = new ReservationPassenger();
    $reservationPassenger->setPassenger($passenger);
    $reservationPassenger->setSeat($seat);

    $reservation = new Reservation();
    $reservation->setFlight($flight);
    $reservation->addReservationPassenger($reservationPassenger);
    $reservationRepository = $this->createMock(ReservationRepository::class);
    $reservationRepository
        ->expects($this->once())
        ->method('find')
        ->with(1)
        ->willReturn($reservation);

    $entityManager->method('getRepository')->willReturn($reservationRepository);

    static::getContainer()->set(
        GetReservationController::class,
        new GetReservationController(
            $entityManager,
            $validator,
            $objectMapper
        )
    );

    $client->jsonRequest('GET', '/api/reservations/1');

    expect($client->getResponse()->getStatusCode())
        ->not->toBe(404);

    expect($client->getResponse()->getStatusCode())
        ->not->toBe(405);
});

it('gets unknown reservation (404).', function () {
    $client = $this->createClient();

    $client->jsonRequest('GET', '/api/reservations/1');

    expect($client->getResponse()->getStatusCode())
        ->toBe(404);
});

it('gets reservation successfully (200).', function () {
    $client = $this->createClient();

    $entityManager = $this->createMock(EntityManagerInterface::class);
    $validator = $this->createMock(ValidatorInterface::class);
    $objectMapper = $this->createMock(ObjectMapperInterface::class);

    $departureAirport = new Airport();
    $arrivalAirport = new Airport();

    $flight = new Flight();
    $flight->setFlightNumber('5J 744');
    $flight->setOriginAirport($departureAirport);
    $flight->setDestinationAirport($arrivalAirport);
    $flight->setDepartureTime(
        new DateTimeImmutable('2026-11-10T06:00:00+08:00')
    );
    $flight->setArrivalTime(
        new DateTimeImmutable('2026-11-10T07:30:00+08:00')
    );

    $passenger = new Passenger();
    $passenger->setFirstName('Juan');
    $passenger->setLastName('Dela Cruz');
    $passenger->setEmail('juan@example.com');

    $seat = new Seat();
    $seat->setCode('A1');

    $reservationPassenger = new ReservationPassenger();
    $reservationPassenger->setPassenger($passenger);
    $reservationPassenger->setSeat($seat);

    $reservation = new Reservation();
    $reservation->setFlight($flight);
    $reservation->addReservationPassenger($reservationPassenger);
    $reservationRepository = $this->createMock(ReservationRepository::class);
    $reservationRepository
        ->expects($this->once())
        ->method('find')
        ->with(1)
        ->willReturn($reservation);

    $entityManager->method('getRepository')->willReturn($reservationRepository);

    static::getContainer()->set(
        GetReservationController::class,
        new GetReservationController(
            $entityManager,
            $validator,
            $objectMapper
        )
    );

    $client->jsonRequest('GET', '/api/reservations/1');

    expect($client->getResponse()->getStatusCode())
        ->toBe(200);
});