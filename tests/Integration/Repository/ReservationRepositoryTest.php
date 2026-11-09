<?php

use App\Entity\Reservation;
use App\Entity\Flight;
use App\Entity\Airport;
use App\Repository\ReservationRepository;
use Doctrine\ORM\EntityManagerInterface;

it('finds reservation by id', function () {
    $container = static::getContainer();

    $entityManager = $container->get(EntityManagerInterface::class);
    $repository = $container->get(ReservationRepository::class);

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

    $foundReservation = $repository->find($reservation->getId());

    expect($foundReservation)
        ->toBeInstanceOf(Reservation::class);

    $entityManager->remove($reservation);
    $entityManager->remove($flight);
    $entityManager->remove($originAirport);
    $entityManager->remove($destinationAirport);
    $entityManager->flush();
});

it('creates a new reservation', function () {
    $container = static::getContainer();

    $entityManager = $container->get(EntityManagerInterface::class);
    $repository = $container->get(ReservationRepository::class);

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

    $foundReservation = $repository->find($reservation->getId());

    expect($foundReservation)->toBeInstanceOf(Reservation::class)
        ->and($foundReservation->getFlight())->toBeInstanceOf(Flight::class);

    $entityManager->remove($reservation);
    $entityManager->remove($flight);
    $entityManager->remove($originAirport);
    $entityManager->remove($destinationAirport);
    $entityManager->flush();
});

it('deletes a reservation', function () {
    $container = static::getContainer();

    $entityManager = $container->get(EntityManagerInterface::class);
    $repository = $container->get(ReservationRepository::class);

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

    $reservationId = $reservation->getId();
    $savedReservation = $repository->find($reservationId);
    $entityManager->remove($savedReservation);
    $entityManager->flush();

    expect($repository->find($reservationId))->toBeNull();

    $entityManager->remove($flight);
    $entityManager->remove($originAirport);
    $entityManager->remove($destinationAirport);
    $entityManager->flush();
});