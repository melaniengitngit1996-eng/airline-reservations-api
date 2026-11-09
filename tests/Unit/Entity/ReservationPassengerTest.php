<?php

use App\Entity\Reservation;
use App\Entity\ReservationPassenger;
use App\Entity\Passenger;
use App\Entity\Seat;

it('stores and returns the reservation', function () {
    $reservationPassenger = new ReservationPassenger();
    $reservation = new Reservation();

    $reservationPassenger->setReservation($reservation);

    expect($reservationPassenger->getReservation())
        ->toBe($reservation);
});

it('stores and returns the passenger', function () {
    $reservationPassenger = new ReservationPassenger();
    $passenger = new Passenger();

    $reservationPassenger->setPassenger($passenger);

    expect($reservationPassenger->getPassenger())
        ->toBe($passenger);
});

it('stores and returns the seat', function () {
    $reservationPassenger = new ReservationPassenger();
    $seat = new Seat();

    $reservationPassenger->setSeat($seat);

    expect($reservationPassenger->getSeat())
        ->toBe($seat);
});

it('has null reservation by default', function () {
    $reservationPassenger = new ReservationPassenger();

    expect($reservationPassenger->getReservation())->toBeNull();
});

it('has null passenger by default', function () {
    $reservationPassenger = new ReservationPassenger();

    expect($reservationPassenger->getPassenger())->toBeNull();
});

it('has null seat by default', function () {
    $reservationPassenger = new ReservationPassenger();

    expect($reservationPassenger->getSeat())->toBeNull();
});

it('has null timestamps by default', function () {
    $reservationPassenger = new ReservationPassenger();

    expect($reservationPassenger->getCreatedAt())->toBeNull();
});