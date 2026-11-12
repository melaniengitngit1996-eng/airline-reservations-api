<?php

use App\Controller\Flight\SearchFlightController;
use App\Entity\Airport;
use App\Entity\Flight;
use App\Repository\AirportRepository;
use App\Repository\FlightRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\ObjectMapper\ObjectMapperInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

it('it has get all flights API endpoint.', function() {
    $client = $this->createClient();

    $client->jsonRequest('GET', '/api/flights', [
        'origin' => 1,
        'destination' => 2,
        'departure_date' => '2026-11-30'
    ]);

    expect($client->getResponse()->getStatusCode())
        ->not->toBe(404);

    expect($client->getResponse()->getStatusCode())
        ->not->toBe(405);
});

it('searches all flights without origin (400)', function() {
    $client = $this->createClient();

    $client->jsonRequest('GET', '/api/flights', [
        'destination' => 2,
        'departure_date' => '2026-11-30'
    ]);

    expect($client->getResponse()->getStatusCode())
        ->toBe(400);

    $response = json_decode(
        $client->getResponse()->getContent(),
        true
    );

    expect($response['message'])
        ->toBe('Required fields are missing.');
});

it('searches all flights without destination (400)', function() {
    $client = $this->createClient();

    $client->jsonRequest('GET', '/api/flights', [
        'origin' => 1,
        'departure_date' => '2026-11-30'
    ]);

    expect($client->getResponse()->getStatusCode())
        ->toBe(400);

    $response = json_decode(
        $client->getResponse()->getContent(),
        true
    );

    expect($response['message'])
        ->toBe('Required fields are missing.');
});

it('searches all flights unknown origin (404)', function() {
    $client = $this->createClient();

    $client->jsonRequest('GET', '/api/flights', [
        'origin' => 100,
        'destination' => 2,
        'departure_date' => '2026-11-30'
    ]);

    expect($client->getResponse()->getStatusCode())
        ->toBe(400);

    $response = json_decode(
        $client->getResponse()->getContent(),
        true
    );

    expect($response['message'])
        ->toBe('Origin airport not found.');
});

it('searches all flights unknown destination (404)', function() {
    $client = $this->createClient();

    $client->jsonRequest('GET', '/api/flights', [
        'origin' => 1,
        'destination' => 200,
        'departure_date' => '2026-11-30'
    ]);

    expect($client->getResponse()->getStatusCode())
        ->toBe(400);

    $response = json_decode(
        $client->getResponse()->getContent(),
        true
    );

    expect($response['message'])
        ->toBe('Destination airport not found.');
});

it('searches all flights without departure date (400)', function() {
    $client = $this->createClient();

    $client->jsonRequest('GET', '/api/flights', [
        'origin' => 1,
        'destination' => 2,
    ]);

    expect($client->getResponse()->getStatusCode())
        ->toBe(400);

    $response = json_decode(
        $client->getResponse()->getContent(),
        true
    );

    expect($response['message'])
        ->toBe('Required fields are missing.');
});

it('searches all flights with same origin and destination airport (400)', function() {
    $client = $this->createClient();

    $client->jsonRequest('GET', '/api/flights', [
        'origin' => 1,
        'destination' => 1,
        'departure_date' => '2026-11-30'
    ]);

    expect($client->getResponse()->getStatusCode())
        ->toBe(400);

    $response = json_decode(
        $client->getResponse()->getContent(),
        true
    );

    expect($response['message'])
        ->toBe('Origin and destination airports cannot be the same.');
});

it('searches all flights with past departure date (400)', function() {
    $client = $this->createClient();

    $client->jsonRequest('GET', '/api/flights', [
        'origin' => 1,
        'destination' => 2,
        'departure_date' => '2026-11-10'
    ]);

    expect($client->getResponse()->getStatusCode())
        ->toBe(400);

    $response = json_decode(
        $client->getResponse()->getContent(),
        true
    );

    expect($response['message'])
        ->toBe('Departure date cannot be in the past.');
});

it('searches all flights with invalid departure date (400)', function() {
    $client = $this->createClient();

    $client->jsonRequest('GET', '/api/flights', [
        'origin' => 1,
        'destination' => 2,
        'departure_date' => '00-11-10'
    ]);

    expect($client->getResponse()->getStatusCode())
        ->toBe(400);

    $response = json_decode(
        $client->getResponse()->getContent(),
        true
    );

    expect($response['message'])
        ->toBe('Invalid departure date.');
});

it('searches flight successfully (400)', function() {
    $client = $this->createClient();

    $entityManager = $this->createMock(EntityManagerInterface::class);
    $validator = $this->createMock(ValidatorInterface::class);
    $objectMapper = $this->createMock(ObjectMapperInterface::class);

    $airport1 = new Airport();
    $airport2 = new Airport();
    $airportRepository = $this->createMock(AirportRepository::class);

    $flight = new Flight();
    $flight->setOriginAirport($airport1);
    $flight->setDestinationAirport($airport2);
    $flight->setDepartureTime(
        new DateTimeImmutable('2026-11-10T06:00:00+08:00')
    );
    $flight->setArrivalTime(
        new DateTimeImmutable('2026-11-10T07:30:00+08:00')
    );
    $flightRepository = $this->createMock(FlightRepository::class);
    
    $entityManager
        ->method('getRepository')
        ->willReturnMap([
            [Airport::class, $airportRepository],
            [Flight::class, $flightRepository],
        ]);

    $airportRepository
    ->expects($this->exactly(2))
    ->method('find')
    ->willReturnMap([
        [1, $airport1],
        [2, $airport2],
    ]);

    $flightRepository
        ->expects($this->once())
        ->method('findByRouteAndDepartureDate')
        ->willReturn([$flight]);

    static::getContainer()->set(
        SearchFlightController::class,
        new SearchFlightController(
            $entityManager,
            $validator,
            $objectMapper
        )
    );

    $client->jsonRequest('GET', '/api/flights', [
        'origin' => 1,
        'destination' => 2,
        'departure_date' => '2026-11-30'
    ]);

    expect($client->getResponse()->getStatusCode())
        ->toBe(200);

    $response = json_decode(
        $client->getResponse()->getContent(),
        true
    );

    expect($response)->toHaveCount(1);
});
