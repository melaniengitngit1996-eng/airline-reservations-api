<?php

use App\Entity\Airport;
use Doctrine\ORM\EntityManagerInterface;

it('automatically sets timestamps when an airport is created', function () {
    $entityManager = static::getContainer()
    ->get(EntityManagerInterface::class);

    $airport = new Airport();
    $airport->setCode('TST');
    $airport->setName('Test Airport');
    $airport->setCity('Test City');
    $airport->setCountry('Philippines');

    $entityManager->persist($airport);
    $entityManager->flush();

    expect($airport->getCreatedAt())->toBeInstanceOf(DateTimeImmutable::class)
        ->and($airport->getUpdatedAt())->toBeInstanceOf(DateTimeImmutable::class)
        ->and($airport->getCreatedAt())->toEqual($airport->getUpdatedAt());

    $entityManager->remove($airport);
    $entityManager->flush();
});

it('updates the updatedAt timestamp when an airport is modified', function () {
    $entityManager = static::getContainer()
        ->get(EntityManagerInterface::class);

    $airport = new Airport();
    $airport->setCode('TST');
    $airport->setName('Test Airport');
    $airport->setCity('Test City');
    $airport->setCountry('Philippines');

    $entityManager->persist($airport);
    $entityManager->flush();

    $createdAt = $airport->getCreatedAt();
    $originalUpdatedAt = $airport->getUpdatedAt();

    // Ensure timestamps will be different.
    sleep(1);

    $airport->setName('Updated Test Airport');

    $entityManager->flush();

    expect($airport->getCreatedAt())
        ->toEqual($createdAt)
        ->and($airport->getUpdatedAt()->getTimestamp())
        ->toBeGreaterThan($originalUpdatedAt->getTimestamp());

    $entityManager->remove($airport);
    $entityManager->flush();
});