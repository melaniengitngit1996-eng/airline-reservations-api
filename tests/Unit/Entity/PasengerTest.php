<?php

use App\Entity\Passenger;

it('stores and returns the first name', function () {
    $passenger = new Passenger();

    $passenger->setFirstName('Juan');

    expect($passenger->getFirstName())->toBe('Juan');
});

it('stores and returns the last name', function () {
    $passenger = new Passenger();

    $passenger->setLastName('Dela Cruz');

    expect($passenger->getLastName())->toBe('Dela Cruz');
});

it('stores and returns the email', function () {
    $passenger = new Passenger();

    $passenger->setEmail('some@email.com');

    expect($passenger->getEmail())->toBe('some@email.com');
});

it('has a null first name by default', function () {
    $passenger = new Passenger();

    expect($passenger->getFirstName())->toBeNull();
});

it('has a null last name by default', function () {
    $passenger = new Passenger();

    expect($passenger->getLastName())->toBeNull();
});

it('has a null email by default', function () {
    $passenger = new Passenger();

    expect($passenger->getEmail())->toBeNull();
});

it('has null timestamps by default', function () {
    $passenger = new Passenger();

    expect($passenger->getCreatedAt())->toBeNull()
        ->and($passenger->getUpdatedAt())->toBeNull();
});