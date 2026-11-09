<?php

use App\Entity\Airport;

it('stores and returns the airport code', function () {
    $airport = new Airport();

    $airport->setCode('MNL');

    expect($airport->getCode())->toBe('MNL');
});

it('stores and returns the airport name', function () {
    $airport = new Airport();

    $airport->setName('Ninoy Aquino International Airport');

    expect($airport->getName())
        ->toBe('Ninoy Aquino International Airport');
});

it('stores and returns the airport city', function () {
    $airport = new Airport();

    $airport->setCity('Manila');

    expect($airport->getCity())->toBe('Manila');
});

it('stores and returns the airport country', function () {
    $airport = new Airport();

    $airport->setCountry('Philippines');

    expect($airport->getCountry())->toBe('Philippines');
});

it('has a null id before being persisted', function () {
    $airport = new Airport();

    expect($airport->getId())->toBeNull();
});

it('has a null code by default', function () {
    $airport = new Airport();

    expect($airport->getCode())->toBeNull();
});

it('has a null name by default', function () {
    $airport = new Airport();

    expect($airport->getName())->toBeNull();
});

it('has a null city by default', function () {
    $airport = new Airport();

    expect($airport->getCity())->toBeNull();
});

it('has a null country by default', function () {
    $airport = new Airport();

    expect($airport->getCountry())->toBeNull();
});

it('has null timestamps by default', function () {
    $airport = new Airport();

    expect($airport->getCreatedAt())->toBeNull()
        ->and($airport->getUpdatedAt())->toBeNull();
});