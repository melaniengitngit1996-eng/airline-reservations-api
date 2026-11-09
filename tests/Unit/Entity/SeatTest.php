<?php

use App\Entity\Seat;
use App\Entity\Flight;

it('stores and returns the code', function () {
    $seat = new Seat();

    $seat->setCode('A1');

    expect($seat->getCode())
        ->toBe('A1');
});

it('stores and returns the flight', function () {
    $seat = new Seat();
    $flight = new Flight();

    $seat->setFlight($flight);

    expect($seat->getFlight())
        ->toBe($flight);
});

it('has null code by default', function () {
    $seat = new Seat();

    expect($seat->getCode())->toBeNull();
});

it('has null flight by default', function () {
    $seat = new Seat();

    expect($seat->getFlight())->toBeNull();
});

it('has null timestamps by default', function () {
    $seat = new Seat();

    expect($seat->getCreatedAt())->toBeNull();
});