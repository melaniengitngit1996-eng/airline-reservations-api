<?php

use App\Entity\Airport;
use App\Entity\Flight;
use App\Repository\AirportRepository;
use App\Repository\FlightRepository;
use App\Service\FlightService;
use Doctrine\ORM\EntityManagerInterface;

it('creates a flight', function () {
    $airportRepository = $this->createMock(AirportRepository::class);
    $flightRepository = $this->createStub(FlightRepository::class);
    $entityManager = $this->createMock(EntityManagerInterface::class);

    $departureAirport = new Airport();
    $arrivalAirport = new Airport();

    $airportRepository->expects($this->exactly(2))
        ->method('find')
        ->willReturnMap([
            [1, $departureAirport],
            [2, $arrivalAirport],
        ]);

    $entityManager->expects($this->once())
        ->method('persist');

    $entityManager->expects($this->once())
        ->method('flush');

    $service = new FlightService(
        $airportRepository,
        $flightRepository,
        $entityManager
    );

    $flight = $service->create([
        'flight_number' => 'PR101',
        'capacity' => 10,
        'departure_airport_id' => 1,
        'arrival_airport_id' => 2,
        'departure_time' => '2026-12-15T08:00:00+08:00',
        'arrival_time' => '2026-12-15T09:30:00+08:00',
    ]);

    expect($flight)->toBeInstanceOf(Flight::class);
});

it('gets all flights', function () {
    $flight = new Flight();

    $flightRepository = $this->createMock(FlightRepository::class);

    $flightRepository->expects($this->once())
        ->method('findAll')
        ->willReturn([$flight]);

    $entityManager = $this->createMock(EntityManagerInterface::class);

    $airportRepository = $this->createMock(AirportRepository::class);

    $service = new FlightService($airportRepository, $flightRepository, $entityManager);

    $flights = $service->findAll();

    expect($flights)->toHaveCount(1);
    expect($flights[0])->toBeInstanceOf(Flight::class);
});

it('get flight by id', function () {
    $flight = new Flight();

    $flightRepository = $this->createMock(FlightRepository::class);
    
    $flightRepository->expects($this->once())
        ->method('find')
        ->with(1)
        ->willReturn($flight);
    
    $airportRepository = $this->createMock(AirportRepository::class);

    $entityManager = $this->createMock(EntityManagerInterface::class);

    $service = new FlightService($airportRepository, $flightRepository, $entityManager);

    $flight = $service->find(1);

    expect($flight)->toBeInstanceOf(Flight::class);
});

it('updates a flight', function () {
    $flight = new Flight();

    $flightRepository = $this->createMock(FlightRepository::class);

    $flightRepository->method('find')
        ->willReturn($flight);

    $departureAirport = new Airport();
    $arrivalAirport = new Airport();

    $airportRepository = $this->createMock(AirportRepository::class);

    $airportRepository->expects($this->exactly(2))
        ->method('find')
        ->willReturnMap([
            [1, $departureAirport],
            [2, $arrivalAirport],
        ]);

    $entityManager = $this->createMock(EntityManagerInterface::class);

    $entityManager->expects($this->once())
        ->method('flush');

    $service = new FlightService($airportRepository, $flightRepository, $entityManager);

    $result = $service->update(1, [
        'capacity' => 10,
        'departure_airport_id' => 1,
        'arrival_airport_id' => 2,
        'departure_time' => '2026-12-15T08:00:00+08:00',
        'arrival_time' => '2026-12-15T09:30:00+08:00',
    ]);

    expect($result->getCapacity())->toBe(10);
});

it('deletes a flight', function () {
    $flight = new Flight();

    $flightRepository = $this->createStub(FlightRepository::class);

    $flightRepository->method('find')
        ->willReturn($flight);

    $entityManager = $this->createMock(EntityManagerInterface::class);

    $airportRepository = $this->createMock(AirportRepository::class);

    $entityManager->expects($this->once())
        ->method('remove')
        ->with($flight);

    $entityManager->expects($this->once())
        ->method('flush');

    $service = new FlightService($airportRepository, $flightRepository, $entityManager);

    $result = $service->delete(1);

    expect($result)->toBeTrue();
});