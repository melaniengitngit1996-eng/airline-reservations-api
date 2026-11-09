<?php

use App\Entity\ReservationPassenger;
use App\Entity\Passenger;
use App\Entity\Reservation;
use App\Entity\Flight;
use App\Entity\Airport;
use App\Entity\Seat;
use App\Repository\ReservationPassengerRepository;
use Doctrine\ORM\EntityManagerInterface;

it('finds passengers by reservation', function () {
    $container = static::getContainer();

    $entityManager = $container->get(EntityManagerInterface::class);
    $repository = $container->get(ReservationPassengerRepository::class);

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

    $seat = new Seat();
    $seat->setFlight($flight);
    $seat->setCode('A1');
    $entityManager->persist($seat);
    $entityManager->flush();

    $reservation = new Reservation();
    $reservation->setFlight($flight);
    $entityManager->persist($reservation);
    $entityManager->flush();

    $passenger = new Passenger();
    $passenger->setFirstName('Juan');
    $passenger->setLastName('Dela Cruz');
    $passenger->setEmail('some@email.com');
    $entityManager->persist($passenger);
    $entityManager->flush();

    $reservationPassenger = new ReservationPassenger();
    $reservationPassenger->setReservation($reservation);
    $reservationPassenger->setPassenger($passenger);
    $reservationPassenger->setSeat($seat);
    $entityManager->persist($reservationPassenger);
    $entityManager->flush();

    // find flight
    $foundPassengers = $repository->findByReservation($reservation);

    expect($foundPassengers)
        ->toHaveCount(1)
        ->and($foundPassengers[0]->getReservation())
        ->toBe($reservation);

    $entityManager->remove($reservationPassenger);
    $entityManager->remove($passenger);
    $entityManager->remove($seat);
    $entityManager->remove($reservation);
    $entityManager->remove($flight);
    $entityManager->remove($originAirport);
    $entityManager->remove($destinationAirport);
    $entityManager->flush();
});

it('adds a new passenger in a reservation', function () {
    $container = static::getContainer();

    $entityManager = $container->get(EntityManagerInterface::class);
    $repository = $container->get(ReservationPassengerRepository::class);

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

    $seat = new Seat();
    $seat->setFlight($flight);
    $seat->setCode('A1');
    $entityManager->persist($seat);
    $entityManager->flush();

    $reservation = new Reservation();
    $reservation->setFlight($flight);
    $entityManager->persist($reservation);
    $entityManager->flush();

    $passenger = new Passenger();
    $passenger->setFirstName('Juan');
    $passenger->setLastName('Dela Cruz');
    $passenger->setEmail('some@email.com');
    $entityManager->persist($passenger);
    $entityManager->flush();

    $reservationPassenger = new ReservationPassenger();
    $reservationPassenger->setReservation($reservation);
    $reservationPassenger->setPassenger($passenger);
    $reservationPassenger->setSeat($seat);
    $entityManager->persist($reservationPassenger);
    $entityManager->flush();

    $foundPassenger = $repository->find($reservationPassenger->getId());

    expect($foundPassenger)->toBeInstanceOf(ReservationPassenger::class)
        ->and($foundPassenger->getReservation())->toBeInstanceOf(Reservation::class);

    $entityManager->remove($reservationPassenger);
    $entityManager->remove($passenger);
    $entityManager->remove($seat);
    $entityManager->remove($reservation);
    $entityManager->remove($flight);
    $entityManager->remove($originAirport);
    $entityManager->remove($destinationAirport);
    $entityManager->flush();
});

it('updates passenger seat in a reservation', function () {
    $container = static::getContainer();

    $entityManager = $container->get(EntityManagerInterface::class);
    $repository = $container->get(ReservationPassengerRepository::class);

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

    $seat = new Seat();
    $seat->setFlight($flight);
    $seat->setCode('A1');
    $entityManager->persist($seat);

    $secondSeat = new Seat();
    $secondSeat->setFlight($flight);
    $secondSeat->setCode('A2');
    $entityManager->persist($secondSeat);
    $entityManager->flush();

    $reservation = new Reservation();
    $reservation->setFlight($flight);
    $entityManager->persist($reservation);
    $entityManager->flush();

    $passenger = new Passenger();
    $passenger->setFirstName('Juan');
    $passenger->setLastName('Dela Cruz');
    $passenger->setEmail('some@email.com');
    $entityManager->persist($passenger);
    $entityManager->flush();

    $reservationPassenger = new ReservationPassenger();
    $reservationPassenger->setReservation($reservation);
    $reservationPassenger->setPassenger($passenger);
    $reservationPassenger->setSeat($seat);
    $entityManager->persist($reservationPassenger);
    $entityManager->flush();

    $savedPassenger = $repository->find($reservationPassenger->getId());
    $savedPassenger->setSeat($secondSeat);
    $entityManager->flush();

    expect($savedPassenger->getSeat())
        ->toBe($secondSeat)
        ->toBeInstanceOf(Seat::class);

    $entityManager->remove($reservationPassenger);
    $entityManager->remove($passenger);
    $entityManager->remove($seat);
    $entityManager->remove($secondSeat);
    $entityManager->remove($reservation);
    $entityManager->remove($flight);
    $entityManager->remove($originAirport);
    $entityManager->remove($destinationAirport);
    $entityManager->flush();
});

it('removes a passenger in a reservation', function () {
    $container = static::getContainer();

    $entityManager = $container->get(EntityManagerInterface::class);
    $repository = $container->get(ReservationPassengerRepository::class);

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

    $seat = new Seat();
    $seat->setFlight($flight);
    $seat->setCode('A1');
    $entityManager->persist($seat);
    $entityManager->flush();

    $reservation = new Reservation();
    $reservation->setFlight($flight);
    $entityManager->persist($reservation);
    $entityManager->flush();

    $passenger = new Passenger();
    $passenger->setFirstName('Juan');
    $passenger->setLastName('Dela Cruz');
    $passenger->setEmail('some@email.com');
    $entityManager->persist($passenger);
    $entityManager->flush();

    $reservationPassenger = new ReservationPassenger();
    $reservationPassenger->setReservation($reservation);
    $reservationPassenger->setPassenger($passenger);
    $reservationPassenger->setSeat($seat);
    $entityManager->persist($reservationPassenger);
    $entityManager->flush();

    $reservationPassengerId = $reservationPassenger->getId();
    $savedReservationPassenger = $repository->find($reservationPassengerId);
    $entityManager->remove($savedReservationPassenger);
    $entityManager->flush();

    expect($repository->find($reservationPassengerId))->toBeNull();

    $entityManager->remove($passenger);
    $entityManager->remove($seat);
    $entityManager->remove($reservation);
    $entityManager->remove($flight);
    $entityManager->remove($originAirport);
    $entityManager->remove($destinationAirport);
    $entityManager->flush();
});