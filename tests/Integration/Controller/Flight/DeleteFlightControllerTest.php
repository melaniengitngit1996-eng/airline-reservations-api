<?php

use App\Controller\Flight\DeleteFlightController;
use App\Entity\Flight;
use App\Entity\Reservation;
use App\Repository\FlightRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\ObjectMapper\ObjectMapperInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

it('has delete flight API endpoint.', function() {
    $client = $this->createClient();

    $entityManager = $this->createMock(EntityManagerInterface::class);
    $validator = $this->createMock(ValidatorInterface::class);
    $objectMapper = $this->createMock(ObjectMapperInterface::class);

    $flightRepository = $this->createMock(FlightRepository::class);
    $flight = new Flight();
    $flightRepository
        ->expects($this->once())
        ->method('find')
        ->with(1)
        ->willReturn($flight);

    $reservationRepository = $this->createMock(FlightRepository::class);
    $reservationRepository
        ->expects($this->once())
        ->method('findBy')
        ->with([
            'flight' => $flight,
        ])
        ->willReturn([]);

    $entityManager
        ->expects($this->exactly(2))
        ->method('getRepository')
        ->willReturnMap([
            [Flight::class, $flightRepository],
            [Reservation::class, $reservationRepository]
        ]);

    $entityManager->expects($this->once())->method('flush');

    static::getContainer()->set(
        DeleteFlightController::class,
        new DeleteFlightController(
            $entityManager,
            $validator,
            $objectMapper
        )
    );

    $client->jsonRequest('DELETE', '/api/flights/1');

    expect($client->getResponse()->getStatusCode())
        ->not->toBe(404);

    expect($client->getResponse()->getStatusCode())
        ->not->toBe(405);
});

it('deletes unknown flight (404)', function () {
    $client = $this->createClient();

    $client->jsonRequest('DELETE', '/api/flights/100');

    expect($client->getResponse()->getStatusCode())
        ->toBe(404);

    $response = json_decode(
        $client->getResponse()->getContent(),
        true
    );

    expect($response['message'])
        ->toBe('Flight not found.');
});

it('deletes flight with reservations (409)', function () {
    $client = $this->createClient();

    $entityManager = $this->createMock(EntityManagerInterface::class);
    $validator = $this->createMock(ValidatorInterface::class);
    $objectMapper = $this->createMock(ObjectMapperInterface::class);

    $flightRepository = $this->createMock(FlightRepository::class);
    $flight = new Flight();
    $flightRepository
        ->expects($this->once())
        ->method('find')
        ->with(1)
        ->willReturn($flight);

    $reservation = new Reservation();
    $reservationRepository = $this->createMock(FlightRepository::class);
    $reservationRepository
        ->expects($this->once())
        ->method('findBy')
        ->with([
            'flight' => $flight,
        ])
        ->willReturn([
            $reservation
        ]);

    $entityManager
        ->expects($this->exactly(2))
        ->method('getRepository')
        ->willReturnMap([
            [Flight::class, $flightRepository],
            [Reservation::class, $reservationRepository]
        ]);

    static::getContainer()->set(
        DeleteFlightController::class,
        new DeleteFlightController(
            $entityManager,
            $validator,
            $objectMapper
        )
    );

    $client->jsonRequest('DELETE', '/api/flights/1');

    expect($client->getResponse()->getStatusCode())
        ->toBe(409);

    $response = json_decode(
        $client->getResponse()->getContent(),
        true
    );

    expect($response['message'])
        ->toBe('This flight has an existing reservations.');
});

it('successfully deletes flight (204)', function () {
    $client = $this->createClient();

    $entityManager = $this->createMock(EntityManagerInterface::class);
    $validator = $this->createMock(ValidatorInterface::class);
    $objectMapper = $this->createMock(ObjectMapperInterface::class);

    $flightRepository = $this->createMock(FlightRepository::class);
    $flight = new Flight();
    $flightRepository
        ->expects($this->once())
        ->method('find')
        ->with(1)
        ->willReturn($flight);

    $reservationRepository = $this->createMock(FlightRepository::class);
    $reservationRepository
        ->expects($this->once())
        ->method('findBy')
        ->with([
            'flight' => $flight,
        ])
        ->willReturn([]);

    $entityManager
        ->expects($this->exactly(2))
        ->method('getRepository')
        ->willReturnMap([
            [Flight::class, $flightRepository],
            [Reservation::class, $reservationRepository]
        ]);

    $entityManager->expects($this->once())->method('flush');

    static::getContainer()->set(
        DeleteFlightController::class,
        new DeleteFlightController(
            $entityManager,
            $validator,
            $objectMapper
        )
    );

    $client->jsonRequest('DELETE', '/api/flights/1');

    expect($client->getResponse()->getStatusCode())
        ->toBe(204);
});