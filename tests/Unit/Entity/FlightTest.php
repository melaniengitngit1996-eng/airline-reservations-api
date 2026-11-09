<?php

use App\Entity\Flight;
use App\Entity\Airport;

it('stores and returns the flight number', function () {
    $flight = new Flight();

    $flight->setFlightNumber('5J 744');

    expect($flight->getFlightNumber())->toBe('5J 744');
});

it('stores and returns the origin airport', function () {
    $flight = new Flight();
    $airport = new Airport();

    $flight->setOriginAirport($airport);

    expect($flight->getOriginAirport())
        ->toBe($airport);
});

it('stores and returns the destination airport', function () {
    $flight = new Flight();
    $airport = new Airport();

    $flight->setDestinationAirport($airport);

    expect($flight->getDestinationAirport())
        ->toBe($airport);
});

it('stores and returns the departure time', function () {
    $flight = new Flight();

    $departureTime = new DateTimeImmutable(
        '2026-11-01T06:00:00+08:00'
    );

    $flight->setDepartureTime($departureTime);

    expect($flight->getDepartureTime())
        ->toBeInstanceOf(DateTimeImmutable::class)
        ->and($flight->getDepartureTime()->format('c'))
        ->toBe('2026-11-01T06:00:00+08:00');
});

it('stores and returns the arrival time', function () {
    $flight = new Flight();

    $arrivalTime = new DateTimeImmutable(
        '2026-11-01T06:00:00+08:00'
    );

    $flight->setArrivalTime($arrivalTime);

    expect($flight->getArrivalTime())
        ->toBeInstanceOf(DateTimeImmutable::class)
        ->and($flight->getArrivalTime()->format('c'))
        ->toBe('2026-11-01T06:00:00+08:00');
});

it('has a null flight number by default', function () {
    $flight = new Flight();

    expect($flight->getFlightNumber())->toBeNull();
});

it('has a null departure time by default', function () {
    $flight = new Flight();

    expect($flight->getDepartureTime())->toBeNull();
});

it('has a null arrival time by default', function () {
    $flight = new Flight();

    expect($flight->getArrivalTime())->toBeNull();
});

it('has a null origin airport by default', function () {
    $flight = new Flight();

    expect($flight->getOriginAirport())->toBeNull();
});

it('has a null destination airport by default', function () {
    $flight = new Flight();

    expect($flight->getDestinationAirport())->toBeNull();
});

it('has null timestamps by default', function () {
    $flight = new Flight();

    expect($flight->getCreatedAt())->toBeNull()
        ->and($flight->getUpdatedAt())->toBeNull();
});