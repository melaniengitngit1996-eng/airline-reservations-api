<?php

use App\Controller\Flight\UpdateFlightController;
use App\Entity\Airport;
use App\Entity\Flight;
use App\Repository\AirportRepository;
use App\Repository\FlightRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\ObjectMapper\ObjectMapperInterface;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

it('has update flight API endpoint', function () {
    $client = $this->createClient();

    $entityManager = $this->createMock(EntityManagerInterface::class);
    $validator = $this->createMock(ValidatorInterface::class);
    $objectMapper = $this->createMock(ObjectMapperInterface::class);

    $flight = new Flight();

    $flightRepository = $this->createMock(FlightRepository::class);
    $flightRepository
        ->expects($this->once())
        ->method('find')
        ->with(1)
        ->willReturn($flight);

    $entityManager
        ->method('getRepository')
        ->willReturn($flightRepository);

    static::getContainer()->set(
        UpdateFlightController::class,
        new UpdateFlightController(
            $entityManager,
            $validator,
            $objectMapper
        )
    );

    $client->jsonRequest('PATCH', '/api/flights/1', [
        'flight_number' => 'PR101',
    ]);

    expect($client->getResponse()->getStatusCode())
        ->not->toBe(404);

    expect($client->getResponse()->getStatusCode())
        ->not->toBe(405);
});

it('updates an unknown flight (404)', function () {
    $client = $this->createClient();

    $entityManager = $this->createMock(EntityManagerInterface::class);
    $validator = $this->createMock(ValidatorInterface::class);
    $objectMapper = $this->createMock(ObjectMapperInterface::class);

    $flightRepository = $this->createMock(FlightRepository::class);
    $flightRepository
        ->expects($this->once())
        ->method('find')
        ->with(100)
        ->willReturn(null);

    $entityManager
        ->method('getRepository')
        ->willReturn($flightRepository);

    static::getContainer()->set(
        UpdateFlightController::class,
        new UpdateFlightController(
            $entityManager,
            $validator,
            $objectMapper
        )
    );

    $client->jsonRequest('PATCH', '/api/flights/100', [
        'flight_number' => 'PR101',
    ]);

    expect($client->getResponse()->getStatusCode())
        ->toBe(404);

    $response = json_decode(
        $client->getResponse()->getContent(),
        true
    );

    expect($response['message'])
        ->toBe('Flight not found.');
});

it('updates a flight with an unknown departure airport (404)', function () {
    $client = $this->createClient();

    $entityManager = $this->createMock(EntityManagerInterface::class);
    $validator = $this->createMock(ValidatorInterface::class);
    $objectMapper = $this->createMock(ObjectMapperInterface::class);

    $flight = new Flight();

    $flightRepository = $this->createMock(FlightRepository::class);
    $flightRepository
        ->expects($this->once())
        ->method('find')
        ->with(1)
        ->willReturn($flight);

    $airportRepository = $this->createMock(AirportRepository::class);
    $airportRepository
        ->expects($this->once())
        ->method('find')
        ->with(100)
        ->willReturn(null);

    $entityManager
        ->method('getRepository')
        ->willReturnMap([
            [Flight::class, $flightRepository],
            [Airport::class, $airportRepository],
        ]);

    static::getContainer()->set(
        UpdateFlightController::class,
        new UpdateFlightController(
            $entityManager,
            $validator,
            $objectMapper
        )
    );

    $client->jsonRequest('PATCH', '/api/flights/1', [
        'departure_airport_id' => 100,
    ]);

    expect($client->getResponse()->getStatusCode())
        ->toBe(404);

    $response = json_decode(
        $client->getResponse()->getContent(),
        true
    );

    expect($response['message'])
        ->toBe('Departure airport not found.');
});

it('updates a flight with an unknown arrival airport (404)', function () {
    $client = $this->createClient();

    $entityManager = $this->createMock(EntityManagerInterface::class);
    $validator = $this->createMock(ValidatorInterface::class);
    $objectMapper = $this->createMock(ObjectMapperInterface::class);

    $flight = new Flight();

    $flightRepository = $this->createMock(FlightRepository::class);
    $flightRepository
        ->expects($this->once())
        ->method('find')
        ->with(1)
        ->willReturn($flight);

    $airportRepository = $this->createMock(AirportRepository::class);
    $airportRepository
        ->expects($this->once())
        ->method('find')
        ->with(2)
        ->willReturn(null);

    $entityManager
        ->method('getRepository')
        ->willReturnMap([
            [Flight::class, $flightRepository],
            [Airport::class, $airportRepository],
        ]);

    static::getContainer()->set(
        UpdateFlightController::class,
        new UpdateFlightController(
            $entityManager,
            $validator,
            $objectMapper
        )
    );

    $client->jsonRequest('PATCH', '/api/flights/1', [
        'arrival_airport_id' => 2,
    ]);

    expect($client->getResponse()->getStatusCode())
        ->toBe(404);

    $response = json_decode(
        $client->getResponse()->getContent(),
        true
    );

    expect($response['message'])
        ->toBe('Arrival airport not found.');
});

it('updates a flight with an existing flight number (409)', function () {
    $client = $this->createClient();

    $entityManager = $this->createMock(EntityManagerInterface::class);
    $validator = $this->createMock(ValidatorInterface::class);
    $objectMapper = $this->createMock(ObjectMapperInterface::class);

    $airport = new Airport();

    $flight = new Flight();
    $flight->setFlightNumber('PR100');
    $flight->setOriginAirport($airport);
    $flight->setDestinationAirport($airport);
    $flight->setDepartureTime(
        new DateTimeImmutable('2026-11-10T06:00:00+08:00')
    );
    $flight->setArrivalTime(
        new DateTimeImmutable('2026-11-10T07:30:00+08:00')
    );

    $existingFlight = new Flight();
    $existingFlight->setFlightNumber('PR101');
    $existingFlight->setOriginAirport($airport);
    $existingFlight->setDestinationAirport($airport);

    $flightRepository = $this->createMock(FlightRepository::class);

    $flightRepository
        ->expects($this->once())
        ->method('find')
        ->with(1)
        ->willReturn($flight);

    $flightRepository
        ->expects($this->once())
        ->method('findOneBy')
        ->with([
            'flightNumber' => 'PR101',
        ])
        ->willReturn($existingFlight);

    $entityManager
        ->method('getRepository')
        ->willReturn($flightRepository);

    $validator
        ->method('validate')
        ->willReturn(new ConstraintViolationList());

    $entityManager
        ->expects($this->never())
        ->method('flush');

    static::getContainer()->set(
        UpdateFlightController::class,
        new UpdateFlightController(
            $entityManager,
            $validator,
            $objectMapper
        )
    );

    $client->jsonRequest('PATCH', '/api/flights/1', [
        'flight_number' => 'PR101',
    ]);

    expect($client->getResponse()->getStatusCode())
        ->toBe(409);

    $response = json_decode(
        $client->getResponse()->getContent(),
        true
    );

    expect($response['message'])
        ->toBe('Flight number already exists.');
});

it('updates flight successfully (200)', function () {
    $client = $this->createClient();

    $entityManager = $this->createMock(EntityManagerInterface::class);
    $validator = $this->createMock(ValidatorInterface::class);
    $objectMapper = $this->createMock(ObjectMapperInterface::class);

    $airport = new Airport();
    $airport->setCode('MNL');
    $airport->setName('Manila');
    $airport->setCity('Manila');
    $airport->setCountry('Philippines');

    $flight = new Flight();
    $flight->setFlightNumber('PR101');
    $flight->setOriginAirport($airport);
    $flight->setDestinationAirport($airport);
    $flight->setDepartureTime(
        new DateTimeImmutable('2026-12-15T08:00:00+08:00')
    );
    $flight->setArrivalTime(
        new DateTimeImmutable('2026-12-15T09:30:00+08:00')
    );
    $flight->setCapacity(5);

    $flightRepository = $this->createMock(FlightRepository::class);
    $flightRepository
        ->expects($this->once())
        ->method('find')
        ->with(1)
        ->willReturn($flight);

    $airportRepository = $this->createMock(AirportRepository::class);
    $airportRepository
        ->expects($this->exactly(2))
        ->method('find')
        ->with(1)
        ->willReturn($airport);

    $entityManager
        ->method('getRepository')
        ->willReturnMap([
            [Flight::class, $flightRepository],
            [Airport::class, $airportRepository],
        ]);

    $validator
        ->expects($this->once())
        ->method('validate')
        ->willReturn(new ConstraintViolationList());

    $entityManager
        ->expects($this->once())
        ->method('flush');

    static::getContainer()->set(
        UpdateFlightController::class,
        new UpdateFlightController(
            $entityManager,
            $validator,
            $objectMapper
        )
    );

    $client->jsonRequest('PATCH', '/api/flights/1', [
        'flight_number' => 'PR101',
        'departure_airport_id' => 1,
        'arrival_airport_id' => 1,
        'departure_time' => '2026-12-15T08:00:00+08:00',
        'arrival_time' => '2026-12-15T09:30:00+08:00',
        'capacity' => 10,
    ]);

    expect($client->getResponse()->getStatusCode())
        ->toBe(200);

    $response = json_decode(
        $client->getResponse()->getContent(),
        true
    );

    expect($response)->toHaveKey('id');
    expect($response['flight_number'])->toBe('PR101');
    expect($response['capacity'])->toBe(10);
});