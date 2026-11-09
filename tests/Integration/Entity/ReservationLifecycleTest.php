<?php

use App\Entity\Reservation;
use App\Entity\Flight;
use App\Entity\Airport;
use Doctrine\ORM\EntityManagerInterface;

it('automatically sets timestamps when an reservation is created', function () {
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

    $reservation = new Reservation();
    $reservation->setFlight($flight);
    $entityManager->persist($reservation);
    $entityManager->flush();

    expect($reservation->getCreatedAt())->toBeInstanceOf(DateTimeImmutable::class);

    $entityManager->remove($reservation);
    $entityManager->remove($flight);
    $entityManager->remove($originAirport);
    $entityManager->remove($destinationAirport);
    $entityManager->flush();
});