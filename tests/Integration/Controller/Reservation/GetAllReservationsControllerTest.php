<?php

use App\Controller\Reservation\GetAllReservationsController;
use App\Entity\Airport;
use App\Entity\Flight;
use App\Entity\Reservation;
use App\Repository\ReservationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\ObjectMapper\ObjectMapperInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

it('it has get all reservations API endpoint', function () {
    $client = $this->createClient();

    $entityManager = $this->createMock(EntityManagerInterface::class);
    $validator = $this->createMock(ValidatorInterface::class);
    $objectMapper = $this->createMock(ObjectMapperInterface::class);

    $flight = new Flight();

    $reservation1 = new Reservation();
    $reservation1->setFlight($flight);

    $reservation2 = new Reservation();
    $reservation2->setFlight($flight);

    $reservationRepository = $this->createMock(ReservationRepository::class);

    $reservationRepository
        ->expects($this->once())
        ->method('findAll')
        ->willReturn([
            $reservation1,
            $reservation2,
        ]);

    $entityManager
        ->method('getRepository')
        ->with(Reservation::class)
        ->willReturn($reservationRepository);

    static::getContainer()->set(
        GetAllReservationsController::class,
        new GetAllReservationsController(
            $entityManager,
            $validator,
            $objectMapper
        )
    );

    $client->jsonRequest('GET', '/api/reservations');

    expect($client->getResponse()->getStatusCode())
        ->not->toBe(404);

    expect($client->getResponse()->getStatusCode())
        ->not->toBe(405);
});

it('gets all reservations with flight_id filter', function () {
    $client = $this->createClient();

    $entityManager = $this->createMock(EntityManagerInterface::class);
    $validator = $this->createMock(ValidatorInterface::class);
    $objectMapper = $this->createMock(ObjectMapperInterface::class);

    $airport = new Airport();

    $flight = new Flight();
    $flight->setDestinationAirport($airport);
    $flight->setOriginAirport($airport);
    $flight->setDepartureTime(
        new DateTimeImmutable('2026-11-10T06:00:00+08:00')
    );
    $flight->setArrivalTime(
        new DateTimeImmutable('2026-11-10T07:30:00+08:00')
    );

    $reservation = new Reservation();
    $reservation->setFlight($flight);

    $reservationRepository = $this->createMock(ReservationRepository::class);

    $reservationRepository
        ->expects($this->once())
        ->method('findByFlightId')
        ->with(1)
        ->willReturn([
            $reservation,
        ]);

    $entityManager
        ->method('getRepository')
        ->with(Reservation::class)
        ->willReturn($reservationRepository);

    static::getContainer()->set(
        GetAllReservationsController::class,
        new GetAllReservationsController(
            $entityManager,
            $validator,
            $objectMapper
        )
    );

    $client->jsonRequest('GET', '/api/reservations', [
        'flight_id' => 1,
    ]);

    expect($client->getResponse()->getStatusCode())
        ->toBe(200);

    $response = json_decode(
        $client->getResponse()->getContent(),
        true
    );

    expect($response)
        ->toHaveCount(1);
});

it('gets all reservations successfully (200)', function () {
    $client = $this->createClient();

    $entityManager = $this->createMock(EntityManagerInterface::class);
    $validator = $this->createMock(ValidatorInterface::class);
    $objectMapper = $this->createMock(ObjectMapperInterface::class);

    $airport = new Airport();

    $flight = new Flight();
    $flight->setDestinationAirport($airport);
    $flight->setOriginAirport($airport);
    $flight->setDepartureTime(
        new DateTimeImmutable('2026-11-10T06:00:00+08:00')
    );
    $flight->setArrivalTime(
        new DateTimeImmutable('2026-11-10T07:30:00+08:00')
    );

    $reservation1 = new Reservation();
    $reservation1->setFlight($flight);

    $reservation2 = new Reservation();
    $reservation2->setFlight($flight);

    $reservationRepository = $this->createMock(ReservationRepository::class);

    $reservationRepository
        ->expects($this->once())
        ->method('findAll')
        ->willReturn([
            $reservation1,
            $reservation2,
        ]);

    $entityManager
        ->method('getRepository')
        ->with(Reservation::class)
        ->willReturn($reservationRepository);

    static::getContainer()->set(
        GetAllReservationsController::class,
        new GetAllReservationsController(
            $entityManager,
            $validator,
            $objectMapper
        )
    );

    $client->jsonRequest('GET', '/api/reservations');

    expect($client->getResponse()->getStatusCode())
        ->toBe(200);

    $response = json_decode(
        $client->getResponse()->getContent(),
        true
    );

    expect($response)
        ->toBeArray();

    expect($response)
        ->toHaveCount(2);
});