<?php

use App\Entity\Seat;
use App\Entity\Flight;
use App\Entity\Airport;
use App\Repository\SeatRepository;
use Doctrine\ORM\EntityManagerInterface;


it('finds all seats by flight id', function () {
    $entityManager = static::getContainer()
        ->get(EntityManagerInterface::class);

    $repository = $entityManager->getRepository(Seat::class);

    $originAirport = new Airport();
    $originAirport->setCode('TST');
    $originAirport->setName('Test Airport');
    $originAirport->setCity('Test City');
    $originAirport->setCountry('Philippines');

    $destinationAirport = new Airport();
    $destinationAirport->setCode('TSA');
    $destinationAirport->setName('Test 2 Airport');
    $destinationAirport->setCity('Test 2 City');
    $destinationAirport->setCountry('Philippines');

    $flight = new Flight();

    $seats = [];

    $entityManager->persist($originAirport);
    $entityManager->persist($destinationAirport);
    $entityManager->flush();

    $flight->setFlightNumber('5J 744');
    $flight->setDepartureTime(
        new DateTimeImmutable('2026-11-01T06:00:00+08:00')
    );
    $flight->setArrivalTime(
        new DateTimeImmutable('2026-11-01T07:00:00+08:00')
    );
    $flight->setOriginAirport($originAirport);
    $flight->setDestinationAirport($destinationAirport);

    $entityManager->persist($flight);
    $entityManager->flush();

    foreach (['A1', 'A2', 'A3'] as $code) {
        $seat = new Seat();
        $seat->setFlight($flight);
        $seat->setCode($code);

        $entityManager->persist($seat);
        $seats[] = $seat;
    }

    $entityManager->flush();

    $foundSeats = $repository->findByFlight($flight);

    expect($foundSeats)
        ->toHaveCount(3)
        ->and($foundSeats[0]->getFlight())
        ->toBe($flight);

    foreach ($seats as $seat) {
        if ($entityManager->contains($seat)) {
            $entityManager->remove($seat);
        }
    }

    $entityManager->remove($flight);
    $entityManager->remove($originAirport);
    $entityManager->remove($destinationAirport);
    $entityManager->flush();
});


it('creates a new seat', function () {
    $container = static::getContainer();

    $entityManager = $container->get(EntityManagerInterface::class);
    $repository = $container->get(SeatRepository::class);

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

    $foundSeat = $repository->find($seat->getId());

    expect($foundSeat)->toBeInstanceOf(Seat::class)
        ->and($foundSeat->getFlight())->toBeInstanceOf(Flight::class);

    $entityManager->remove($seat);
    $entityManager->remove($flight);
    $entityManager->remove($originAirport);
    $entityManager->remove($destinationAirport);
    $entityManager->flush();
});

it('updates a seat', function () {
    $container = static::getContainer();

    $entityManager = $container->get(EntityManagerInterface::class);
    $repository = $container->get(SeatRepository::class);

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

    $foundSeat = $repository->find($seat->getId());
    $foundSeat->setCode('A2');
    $entityManager->persist($foundSeat);
    $entityManager->flush();

    expect($foundSeat)->toBeInstanceOf(Seat::class)
        ->and($foundSeat->getCode())->toBe('A2');

    $entityManager->remove($seat);
    $entityManager->remove($flight);
    $entityManager->remove($originAirport);
    $entityManager->remove($destinationAirport);
    $entityManager->flush();
});

it('deletes a seat', function () {
    $container = static::getContainer();

    $entityManager = $container->get(EntityManagerInterface::class);
    $repository = $container->get(SeatRepository::class);

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

    $seatId = $seat->getId();
    $savedReservation = $repository->find($seatId);
    $entityManager->remove($savedReservation);
    $entityManager->flush();

    expect($repository->find($seatId))->toBeNull();

    $entityManager->remove($flight);
    $entityManager->remove($originAirport);
    $entityManager->remove($destinationAirport);
    $entityManager->flush();
});