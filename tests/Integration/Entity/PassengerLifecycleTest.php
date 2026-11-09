<?php

use App\Entity\Passenger;
use Doctrine\ORM\EntityManagerInterface;

it('automatically sets timestamps when a passenger is created', function () {
    $entityManager = static::getContainer()
    ->get(EntityManagerInterface::class);

    $passenger = new Passenger();
    $passenger->setFirstName('Juan');
    $passenger->setLastName('Dela Cruz');
    $passenger->setEmail('some@email.com');

    $entityManager->persist($passenger);
    $entityManager->flush();

    expect($passenger->getCreatedAt())->toBeInstanceOf(DateTimeImmutable::class)
        ->and($passenger->getUpdatedAt())->toBeInstanceOf(DateTimeImmutable::class)
        ->and($passenger->getCreatedAt())->toEqual($passenger->getUpdatedAt());

    $entityManager->remove($passenger);
    $entityManager->flush();
});

it('updates the updatedAt timestamp when a passenger is modified', function () {
    $entityManager = static::getContainer()
        ->get(EntityManagerInterface::class);

    $passenger = new Passenger();
    $passenger->setFirstName('Juan');
    $passenger->setLastName('Dela Cruz');
    $passenger->setEmail('some@email.com');

    $entityManager->persist($passenger);
    $entityManager->flush();

    $createdAt = $passenger->getCreatedAt();
    $originalUpdatedAt = $passenger->getUpdatedAt();

    sleep(1);

    $passenger->setFirstName('Juan 2');

    $entityManager->flush();

    expect($passenger->getCreatedAt())
        ->toEqual($createdAt)
        ->and($passenger->getUpdatedAt()->getTimestamp())
        ->toBeGreaterThan($originalUpdatedAt->getTimestamp());

    $entityManager->remove($passenger);
    $entityManager->flush();
});