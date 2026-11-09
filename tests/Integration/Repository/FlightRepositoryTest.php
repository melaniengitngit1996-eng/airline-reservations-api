<?php

use App\Entity\Flight;
use App\Entity\Airport;
use App\Repository\FlightRepository;
use Doctrine\ORM\EntityManagerInterface;


it('finds flights by origin, destination and departure date', function () {
    $entityManager = static::getContainer()
        ->get(EntityManagerInterface::class);

    $repository = $entityManager->getRepository(Flight::class);

    // create origin airport
    $originAirport = new Airport();
    $originAirport->setCode('TST');
    $originAirport->setName('Test Origin Airport');
    $originAirport->setCity('Test City');
    $originAirport->setCountry('Philippines');

    // create destination airport
    $destinationAirport = new Airport();
    $destinationAirport->setCode('TS2');
    $destinationAirport->setName('Test Destination Airport');
    $destinationAirport->setCity('Test City 2');
    $destinationAirport->setCountry('Philippines');

    $entityManager->persist($originAirport);
    $entityManager->persist($destinationAirport);
    $entityManager->flush();
    
    // create flight
    $flight = new Flight();
    $flight->setFlightNumber('TST 001');
    $flight->setOriginAirport($originAirport);
    $flight->setDestinationAirport($destinationAirport);
    $flight->setDepartureTime(
        new DateTimeImmutable('2026-11-10T06:00:00+08:00')
    );
    $flight->setArrivalTime(
        new DateTimeImmutable('2026-11-10T07:30:00+08:00')
    );

    $entityManager->persist($flight);
    $entityManager->flush();

    // find flight
    $foundFlights = $repository->findByRouteAndDepartureDate(
        $originAirport,
        $destinationAirport,
        new DateTimeImmutable('2026-11-10')
    );

    expect($foundFlights)
        ->toHaveCount(1)
        ->and($foundFlights[0]->getFlightNumber())
        ->toBe('TST 001');

    $entityManager->remove($flight);
    $entityManager->remove($originAirport);
    $entityManager->remove($destinationAirport);
    $entityManager->flush();
});

it('returns all flights', function () {
    $container = static::getContainer();

    $entityManager = static::getContainer()
        ->get(EntityManagerInterface::class);

    $repository = $entityManager->getRepository(Flight::class);

    // create origin airport
    $originAirport = new Airport();
    $originAirport->setCode('TST');
    $originAirport->setName('Test Origin Airport');
    $originAirport->setCity('Test City');
    $originAirport->setCountry('Philippines');

    // create destination airport
    $destinationAirport = new Airport();
    $destinationAirport->setCode('TS2');
    $destinationAirport->setName('Test Destination Airport');
    $destinationAirport->setCity('Test City 2');
    $destinationAirport->setCountry('Philippines');

    $entityManager->persist($originAirport);
    $entityManager->persist($destinationAirport);
    $entityManager->flush();
    
    // create flight
    $flight = new Flight();
    $flight->setFlightNumber('TST 001');
    $flight->setOriginAirport($originAirport);
    $flight->setDestinationAirport($destinationAirport);
    $flight->setDepartureTime(
        new DateTimeImmutable('2026-11-10T06:00:00+08:00')
    );
    $flight->setArrivalTime(
        new DateTimeImmutable('2026-11-10T07:30:00+08:00')
    );

    $entityManager->persist($flight);
    $entityManager->flush();

    $flights = $repository->findAll();

    expect($flights)->toContain($flight);

    $entityManager->remove($originAirport);
    $entityManager->remove($destinationAirport);
    $entityManager->remove($flight);
    $entityManager->flush();
});

it('creates a new flight', function () {
    $container = static::getContainer();

    $entityManager = static::getContainer()
        ->get(EntityManagerInterface::class);

    $repository = $entityManager->getRepository(Flight::class);

    // create origin airport
    $originAirport = new Airport();
    $originAirport->setCode('TST');
    $originAirport->setName('Test Origin Airport');
    $originAirport->setCity('Test City');
    $originAirport->setCountry('Philippines');

    // create destination airport
    $destinationAirport = new Airport();
    $destinationAirport->setCode('TS2');
    $destinationAirport->setName('Test Destination Airport');
    $destinationAirport->setCity('Test City 2');
    $destinationAirport->setCountry('Philippines');

    $entityManager->persist($originAirport);
    $entityManager->persist($destinationAirport);
    $entityManager->flush();
    
    // create flight
    $flight = new Flight();
    $flight->setFlightNumber('TST 001');
    $flight->setOriginAirport($originAirport);
    $flight->setDestinationAirport($destinationAirport);
    $flight->setDepartureTime(
        new DateTimeImmutable('2026-11-10T06:00:00+08:00')
    );
    $flight->setArrivalTime(
        new DateTimeImmutable('2026-11-10T07:30:00+08:00')
    );

    $entityManager->persist($flight);
    $entityManager->flush();

    $savedFlight = $repository->find($flight->getId());

    expect($savedFlight)->toBeInstanceOf(Flight::class)
        ->and($savedFlight->getFlightNumber())->toBe('TST 001');

    $entityManager->remove($originAirport);
    $entityManager->remove($destinationAirport);
    $entityManager->remove($flight);
    $entityManager->flush();
});

it('updates a flight', function () {
    $container = static::getContainer();

    $container = static::getContainer();

    $entityManager = static::getContainer()
        ->get(EntityManagerInterface::class);

    $repository = $entityManager->getRepository(Flight::class);

    // create origin airport
    $originAirport = new Airport();
    $originAirport->setCode('TST');
    $originAirport->setName('Test Origin Airport');
    $originAirport->setCity('Test City');
    $originAirport->setCountry('Philippines');

    // create destination airport
    $destinationAirport = new Airport();
    $destinationAirport->setCode('TS2');
    $destinationAirport->setName('Test Destination Airport');
    $destinationAirport->setCity('Test City 2');
    $destinationAirport->setCountry('Philippines');

    $entityManager->persist($originAirport);
    $entityManager->persist($destinationAirport);
    $entityManager->flush();
    
    // create flight
    $flight = new Flight();
    $flight->setFlightNumber('TST 001');
    $flight->setOriginAirport($originAirport);
    $flight->setDestinationAirport($destinationAirport);
    $flight->setDepartureTime(
        new DateTimeImmutable('2026-11-10T06:00:00+08:00')
    );
    $flight->setArrivalTime(
        new DateTimeImmutable('2026-11-10T07:30:00+08:00')
    );

    $entityManager->persist($flight);
    $entityManager->flush();

    $savedFlight = $repository->find($flight->getId());

    expect($savedFlight)->toBeInstanceOf(Flight::class);

    $savedFlight->setFlightNumber('TST 002');

    $entityManager->flush();

    expect($savedFlight->getFlightNumber())
        ->toBe('TST 002');

    $entityManager->remove($originAirport);
    $entityManager->remove($destinationAirport);
    $entityManager->remove($flight);
    $entityManager->flush();
});

it('deletes a flight', function () {
    $container = static::getContainer();

    $entityManager = static::getContainer()
        ->get(EntityManagerInterface::class);

    $repository = $entityManager->getRepository(Flight::class);

    // create origin airport
    $originAirport = new Airport();
    $originAirport->setCode('TST');
    $originAirport->setName('Test Origin Airport');
    $originAirport->setCity('Test City');
    $originAirport->setCountry('Philippines');

    // create destination airport
    $destinationAirport = new Airport();
    $destinationAirport->setCode('TS2');
    $destinationAirport->setName('Test Destination Airport');
    $destinationAirport->setCity('Test City 2');
    $destinationAirport->setCountry('Philippines');

    $entityManager->persist($originAirport);
    $entityManager->persist($destinationAirport);
    $entityManager->flush();
    
    // create flight
    $flight = new Flight();
    $flight->setFlightNumber('TST 001');
    $flight->setOriginAirport($originAirport);
    $flight->setDestinationAirport($destinationAirport);
    $flight->setDepartureTime(
        new DateTimeImmutable('2026-11-10T06:00:00+08:00')
    );
    $flight->setArrivalTime(
        new DateTimeImmutable('2026-11-10T07:30:00+08:00')
    );

    $entityManager->persist($flight);
    $entityManager->flush();

    $flightId = $flight->getId();
    $savedFlight = $repository->find($flightId);
    $entityManager->remove($savedFlight);
    $entityManager->flush();

    expect($repository->find($flightId))->toBeNull();

    $entityManager->remove($originAirport);
    $entityManager->remove($destinationAirport);
    $entityManager->flush();
});