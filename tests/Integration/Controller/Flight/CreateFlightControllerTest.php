<?php

use App\Controller\Flight\CreateFlightController;
use App\Entity\Airport;
use App\Entity\Flight;
use App\Repository\AirportRepository;
use App\Repository\FlightRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\ObjectMapper\ObjectMapperInterface;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

it('has create flight API endpoint', function () {
    $client = $this->createClient();

    $client->jsonRequest('POST', '/api/flights', []);

    expect($client->getResponse()->getStatusCode())
        ->not->toBe(404);
});

it('creates a flight with blank fields (400)', function () {
    $client = $this->createClient();

    $client->jsonRequest('POST', '/api/flights', [
        'flight_number' => '',
        'departure_airport_id' => 1,
        'arrival_airport_id' => 1,
        'departure_time' => '2026-12-15T08:00:00+08:00',
        'arrival_time' => '2026-12-15T09:30:00+08:00',
        'capacity' => 5
    ]);

    expect($client->getResponse()->getStatusCode())->toBe(400);

    $response = json_decode(
        $client->getResponse()->getContent(),
        true
    );

    expect($response['message'])
        ->toBe('Required fields are missing.');
});

it('creates a flight with empty fields (400)', function () {
    $client = $this->createClient();

    $client->jsonRequest('POST', '/api/flights', []);

    expect($client->getResponse()->getStatusCode())->toBe(400);

    $response = json_decode(
        $client->getResponse()->getContent(),
        true
    );

    expect($response['message'])
        ->toBe('Required fields are missing.');
});

it('creates a flight with missing fields (400)', function () {
    $client = $this->createClient();

    $client->jsonRequest('POST', '/api/flights', [
        'departure_airport_id' => 1,
        'arrival_airport_id' => 1,
        'departure_time' => '2026-12-15T08:00:00+08:00',
        'arrival_time' => '2026-12-15T09:30:00+08:00',
        'capacity' => 5
    ]);

    expect($client->getResponse()->getStatusCode())->toBe(400);

    $response = json_decode(
        $client->getResponse()->getContent(),
        true
    );

    expect($response['message'])
        ->toBe('Required fields are missing.');
});

it('creates a flight with invalid departure time (400)', function () {
    $client = $this->createClient();

    $client->jsonRequest('POST', '/api/flights', [
        'flight_number' => 'PR101',
        'departure_airport_id' => 1,
        'arrival_airport_id' => 1,
        'departure_time' => '2026-12-15',
        'arrival_time' => '2026-12-15T09:30:00+08:00',
        'capacity' => 5
    ]);

    expect($client->getResponse()->getStatusCode())->toBe(400);

    $response = json_decode(
        $client->getResponse()->getContent(),
        true
    );

    expect($response['message'])
        ->toBe('Invalid departure time.');
});

it('creates a flight with invalid arrival time (400)', function () {
    $client = $this->createClient();

    $client->jsonRequest('POST', '/api/flights', [
        'flight_number' => 'PR101',
        'departure_airport_id' => 1,
        'arrival_airport_id' => 1,
        'departure_time' => '2026-12-15T08:00:00+08:00',
        'arrival_time' => '2026-12-15',
        'capacity' => 5
    ]);

    expect($client->getResponse()->getStatusCode())->toBe(400);

    $response = json_decode(
        $client->getResponse()->getContent(),
        true
    );

    expect($response['message'])
        ->toBe('Invalid arrival time.');
});

it('creates a flight with unknown departure airport (404)', function () {
    $client = $this->createClient();

    $client->jsonRequest('POST', '/api/flights', [
        'flight_number' => 'PR101',
        'departure_airport_id' => 101,
        'arrival_airport_id' => 1,
        'departure_time' => '2026-12-15T08:00:00+08:00',
        'arrival_time' => '2026-12-15T09:30:00+08:00',
        'capacity' => 5
    ]);

    expect($client->getResponse()->getStatusCode())->toBe(404);

    $response = json_decode(
        $client->getResponse()->getContent(),
        true
    );

    expect($response['message'])
        ->toBe('Airport not found.');
});

it('creates a flight with unknown arrival airport (404)', function () {
    $client = $this->createClient();

    $client->jsonRequest('POST', '/api/flights', [
        'flight_number' => 'PR101',
        'departure_airport_id' => 1,
        'arrival_airport_id' => 101,
        'departure_time' => '2026-12-15T08:00:00+08:00',
        'arrival_time' => '2026-12-15T09:30:00+08:00',
        'capacity' => 5
    ]);

    expect($client->getResponse()->getStatusCode())->toBe(404);

    $response = json_decode(
        $client->getResponse()->getContent(),
        true
    );

    expect($response['message'])
        ->toBe('Airport not found.');
});

it('creates a flight with an existing flight number (409)', function () {
    $client = $this->createClient();

    $client->jsonRequest('POST', '/api/flights', [
        'flight_number' => '5J 561',
        'departure_airport_id' => 1,
        'arrival_airport_id' => 1,
        'departure_time' => '2026-12-15T08:00:00+08:00',
        'arrival_time' => '2026-12-15T09:30:00+08:00',
        'capacity' => 5
    ]);

    expect($client->getResponse()->getStatusCode())->toBe(409);

    $response = json_decode(
        $client->getResponse()->getContent(),
        true
    );

    expect($response['message'])
        ->toBe('Flight number already exists.');
});

it('create flights successfully (201)', function () {
    $client = $this->createClient();
    $airport = new Airport();
    $flight = new Flight();

    $airportRepository = $this->createMock(AirportRepository::class);
    $airportRepository->expects($this->exactly(2))->method('find')->willReturn($airport);
    
    $entityManager = $this->createMock(EntityManagerInterface::class);
    $entityManager->method('getRepository')->willReturn($airportRepository);

    $validator = $this->createMock(ValidatorInterface::class);
    $validator->expects($this->exactly(2))->method('validate')->willReturn(new ConstraintViolationList());

    $flight = $this->createMock(Flight::class);

    $flight->expects($this->once())->method('getOriginAirport')->willReturn($airport);
    $flight->expects($this->once())->method('getDestinationAirport')->willReturn($airport);

    $flight->method('getFlightNumber')->willReturn('PR101');

    $objectMapper = $this->createMock(ObjectMapperInterface::class);
    $objectMapper->method('map')->willReturn($flight);

    static::getContainer()->set(
        CreateFlightController::class,
        new CreateFlightController(
            $entityManager,
            $validator,
            $objectMapper
        )
    );

    $client->jsonRequest('POST', '/api/flights', [
        'flight_number' => 'PR101',
        'departure_airport_id' => 1,
        'arrival_airport_id' => 1,
        'departure_time' => '2026-12-15T08:00:00+08:00',
        'arrival_time' => '2026-12-15T09:30:00+08:00',
        'capacity' => 5
    ]);

    expect($client->getResponse()->getStatusCode())->toBe(201);
    
    $response = json_decode(
        $client->getResponse()->getContent(),
        true
    );

    expect($response['flight_number'])->toBe('PR101');
});