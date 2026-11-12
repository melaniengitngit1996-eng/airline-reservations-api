<?php

use App\Controller\Flight\DeleteFlightController;
use App\Controller\Flight\GetFlightController;
use App\Entity\Airport;
use App\Entity\Flight;
use App\Entity\Reservation;
use App\Repository\FlightRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\ObjectMapper\ObjectMapperInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

it('has get flight API endpoint.', function() {
    $client = $this->createClient();

    $entityManager = $this->createMock(EntityManagerInterface::class);
    $validator = $this->createMock(ValidatorInterface::class);
    $objectMapper = $this->createMock(ObjectMapperInterface::class);

    $airport = new Airport();

    $flightRepository = $this->createMock(FlightRepository::class);

    $flight = new Flight();
    $flight->setDestinationAirport($airport);
    $flight->setOriginAirport($airport);
    $flight->setDepartureTime(
        new DateTimeImmutable('2026-11-10T06:00:00+08:00')
    );
    $flight->setArrivalTime(
        new DateTimeImmutable('2026-11-10T07:30:00+08:00')
    );

    $flightRepository
        ->expects($this->once())
        ->method('find')
        ->with(1)
        ->willReturn($flight);

    $entityManager
        ->expects($this->once())
        ->method('getRepository')
        ->willReturn($flightRepository);

    static::getContainer()->set(
        GetFlightController::class,
        new GetFlightController(
            $entityManager,
            $validator,
            $objectMapper
        )
    );

    $client->jsonRequest('GET', '/api/flights/1');

    expect($client->getResponse()->getStatusCode())
        ->not->toBe(404);

    expect($client->getResponse()->getStatusCode())
        ->not->toBe(405);
});

it('gets unknown flight (404)', function () {
    $client = $this->createClient();

    $client->jsonRequest('GET', '/api/flights/100');

    expect($client->getResponse()->getStatusCode())
        ->toBe(404);

    $response = json_decode(
        $client->getResponse()->getContent(),
        true
    );

    expect($response['message'])
        ->toBe('Flight not found.');
});

it('gets flight successfully (200)', function() {
    $client = $this->createClient();

    $entityManager = $this->createMock(EntityManagerInterface::class);
    $validator = $this->createMock(ValidatorInterface::class);
    $objectMapper = $this->createMock(ObjectMapperInterface::class);

    $airport = new Airport();

    $flightRepository = $this->createMock(FlightRepository::class);

    $flight = new Flight();
    $flight->setDestinationAirport($airport);
    $flight->setOriginAirport($airport);
    $flight->setDepartureTime(
        new DateTimeImmutable('2026-11-10T06:00:00+08:00')
    );
    $flight->setArrivalTime(
        new DateTimeImmutable('2026-11-10T07:30:00+08:00')
    );

    $flightRepository
        ->expects($this->once())
        ->method('find')
        ->with(1)
        ->willReturn($flight);

    $entityManager
        ->expects($this->once())
        ->method('getRepository')
        ->willReturn($flightRepository);

    static::getContainer()->set(
        GetFlightController::class,
        new GetFlightController(
            $entityManager,
            $validator,
            $objectMapper
        )
    );

    $client->jsonRequest('GET', '/api/flights/1');

    expect($client->getResponse()->getStatusCode())
        ->toBe(200);

    $response = json_decode(
        $client->getResponse()->getContent(),
        true
    );

    expect($response)
        ->toHaveKey('id');
});