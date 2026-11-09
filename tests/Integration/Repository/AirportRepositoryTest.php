<?php

use App\Entity\Airport;
use App\Repository\AirportRepository;
use Doctrine\ORM\EntityManagerInterface;

it('finds an airport by its code', function () {
    $container = static::getContainer();

    $entityManager = $container->get(EntityManagerInterface::class);
    $repository = $container->get(AirportRepository::class);

    $airport = new Airport();
    $airport->setCode('TST');
    $airport->setName('Test Airport');
    $airport->setCity('Test City');
    $airport->setCountry('Philippines');

    try {
        $entityManager->persist($airport);
        $entityManager->flush();

        $foundAirport = $repository->findOneBy([
            'code' => 'TST',
        ]);

        expect($foundAirport)
            ->toBeInstanceOf(Airport::class)
            ->and($foundAirport->getCode())
            ->toBe('TST');
    } finally {
        if ($entityManager->contains($airport)) {
            $entityManager->remove($airport);
            $entityManager->flush();
        }
    }
});

it('returns all airports', function () {
    $container = static::getContainer();

    $entityManager = $container->get(EntityManagerInterface::class);
    $repository = $container->get(AirportRepository::class);

    $airport = new Airport();
    $airport->setCode('TST');
    $airport->setName('Test Airport');
    $airport->setCity('Test City');
    $airport->setCountry('Philippines');

    try {
        $entityManager->persist($airport);
        $entityManager->flush();

        $airports = $repository->findAll();

        expect($airports)->toContain($airport);
    } finally {
        if ($entityManager->contains($airport)) {
            $entityManager->remove($airport);
            $entityManager->flush();
        }
    }
});

it('creates a new airport', function () {
    $container = static::getContainer();

    $entityManager = $container->get(EntityManagerInterface::class);
    $repository = $container->get(AirportRepository::class);

    $airport = new Airport();
    $airport->setCode('TST');
    $airport->setName('Test Airport');
    $airport->setCity('Test City');
    $airport->setCountry('Philippines');

    expect($airport->getId())->toBeNull();

    $entityManager->persist($airport);
    $entityManager->flush();

    $savedAirport = $repository->find($airport->getId());

    expect($savedAirport)->toBeInstanceOf(Airport::class)
        ->and($savedAirport->getCode())->toBe('TST');

    $entityManager->remove($airport);
    $entityManager->flush();
});

it('updates an airport', function () {
    $container = static::getContainer();

    $entityManager = $container->get(EntityManagerInterface::class);
    $repository = $container->get(AirportRepository::class);

    $airport = new Airport();
    $airport->setCode('TST');
    $airport->setName('Test Airport');
    $airport->setCity('Test City');
    $airport->setCountry('Philippines');

    $entityManager->persist($airport);
    $entityManager->flush();

    $savedAirport = $repository->find($airport->getId());

    expect($savedAirport)->toBeInstanceOf(Airport::class);

    $savedAirport->setName('Updated Test Airport');

    $entityManager->flush();

    expect($savedAirport->getName())
        ->toBe('Updated Test Airport')
        ->and($savedAirport->getCode())
        ->toBe('TST');

    $entityManager->remove($airport);
    $entityManager->flush();
});

it('deletes an airport', function () {
    $container = static::getContainer();

    $entityManager = $container->get(EntityManagerInterface::class);
    $repository = $container->get(AirportRepository::class);

    $airport = new Airport();
    $airport->setCode('TST');
    $airport->setName('Test Airport');
    $airport->setCity('Test City');
    $airport->setCountry('Philippines');

    $entityManager->persist($airport);
    $entityManager->flush();

    $airportId = $airport->getId();
    $savedAirport = $repository->find($airportId);
    $entityManager->remove($savedAirport);
    $entityManager->flush();

    expect($repository->find($airportId))->toBeNull();
});