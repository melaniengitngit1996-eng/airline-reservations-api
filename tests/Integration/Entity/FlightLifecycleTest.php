<?php

use App\Entity\Flight;
use App\Entity\Airport;
use Doctrine\ORM\EntityManagerInterface;

it('automatically sets timestamps when a flight is created', function () {
    $entityManager = static::getContainer()
    ->get(EntityManagerInterface::class);

    $originAirport = new Airport();
    $originAirport->setCode('TST');
    $originAirport->setName('Test Airport');
    $originAirport->setCity('Test City');
    $originAirport->setCountry('Philippines');

    $entityManager->persist($originAirport);

    $destinationAirport = new Airport();
    $destinationAirport->setCode('TSA');
    $destinationAirport->setName('Test 2 Airport');
    $destinationAirport->setCity('Test 2 City');
    $destinationAirport->setCountry('Philippines');

    $entityManager->persist($destinationAirport);
    $entityManager->flush();

    $departureTime = new DateTimeImmutable('2026-11-01T06:00:00+08:00');
    $arrivalTime = new DateTimeImmutable('2026-11-01T07:00:00+08:00');

    $flight = new Flight();
    $flight->setFlightNumber('5J 744');
    $flight->setDepartureTime($departureTime);
    $flight->setArrivalTime($arrivalTime);
    $flight->setOriginAirport($originAirport);
    $flight->setDestinationAirport($destinationAirport);

    $entityManager->persist($flight);
    $entityManager->flush();

    expect($flight->getCreatedAt())->toBeInstanceOf(DateTimeImmutable::class)
        ->and($flight->getUpdatedAt())->toBeInstanceOf(DateTimeImmutable::class)
        ->and($flight->getCreatedAt())->toEqual($flight->getUpdatedAt());

    $entityManager->remove($flight);
    $entityManager->remove($originAirport);
    $entityManager->remove($destinationAirport);
    $entityManager->flush();
});

it('updates the updatedAt timestamp when a flight is modified', function () {
    $entityManager = static::getContainer()
        ->get(EntityManagerInterface::class);

    $originAirport = new Airport();
    $originAirport->setCode('TST');
    $originAirport->setName('Test Airport');
    $originAirport->setCity('Test City');
    $originAirport->setCountry('Philippines');

    $entityManager->persist($originAirport);

    $destinationAirport = new Airport();
    $destinationAirport->setCode('TSA');
    $destinationAirport->setName('Test 2 Airport');
    $destinationAirport->setCity('Test 2 City');
    $destinationAirport->setCountry('Philippines');

    $entityManager->persist($destinationAirport);
    $entityManager->flush();

    $departureTime = new DateTimeImmutable('2026-11-01T06:00:00+08:00');
    $arrivalTime = new DateTimeImmutable('2026-11-01T07:00:00+08:00');

    $flight = new Flight();
    $flight->setFlightNumber('5J 744');
    $flight->setDepartureTime($departureTime);
    $flight->setArrivalTime($arrivalTime);
    $flight->setOriginAirport($originAirport);
    $flight->setDestinationAirport($destinationAirport);

    $entityManager->persist($flight);
    $entityManager->flush();

    $createdAt = $flight->getCreatedAt();
    $originalUpdatedAt = $flight->getUpdatedAt();

    // Ensure timestamps will be different.
    sleep(1);

    $flight->setFlightNumber('TST 002');

    $entityManager->flush();

    expect($flight->getCreatedAt())
        ->toEqual($createdAt)
        ->and($flight->getUpdatedAt()->getTimestamp())
        ->toBeGreaterThan($originalUpdatedAt->getTimestamp());

    $entityManager->remove($flight);
    $entityManager->remove($originAirport);
    $entityManager->remove($destinationAirport);
    $entityManager->flush();
});