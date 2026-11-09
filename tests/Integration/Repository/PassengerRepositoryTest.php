<?php

use App\Entity\Passenger;
use App\Repository\PassengerRepository;
use Doctrine\ORM\EntityManagerInterface;

it('returns all passengers', function () {
    $container = static::getContainer();

    $entityManager = $container->get(EntityManagerInterface::class);
    $repository = $container->get(PassengerRepository::class);

    $passenger = new Passenger();
    $passenger->setFirstName('Juan');
    $passenger->setLastName('Dela Cruz');
    $passenger->setEmail('some@email.com');

    try {
        $entityManager->persist($passenger);
        $entityManager->flush();

        $passengers = $repository->findAll();

        expect($passengers)->toContain($passenger);
    } finally {
        if ($entityManager->contains($passenger)) {
            $entityManager->remove($passenger);
            $entityManager->flush();
        }
    }
});

it('creates a new passenger', function () {
    $container = static::getContainer();

    $entityManager = $container->get(EntityManagerInterface::class);
    $repository = $container->get(PassengerRepository::class);

    $passenger = new Passenger();
    $passenger->setFirstName('Juan');
    $passenger->setLastName('Dela Cruz');
    $passenger->setEmail('some@email.com');

    expect($passenger->getId())->toBeNull();

    $entityManager->persist($passenger);
    $entityManager->flush();

    $savedPassenger = $repository->find($passenger->getId());

    expect($savedPassenger)->toBeInstanceOf(Passenger::class)
        ->and($savedPassenger->getFirstName())->toBe('Juan');

    $entityManager->remove($passenger);
    $entityManager->flush();
});

it('updates a passenger', function () {
    $container = static::getContainer();

    $entityManager = $container->get(EntityManagerInterface::class);
    $repository = $container->get(PassengerRepository::class);

    $passenger = new Passenger();
    $passenger->setFirstName('Juan');
    $passenger->setLastName('Dela Cruz');
    $passenger->setEmail('some@email.com');

    $entityManager->persist($passenger);
    $entityManager->flush();

    $savedPassenger = $repository->find($passenger->getId());

    expect($savedPassenger)->toBeInstanceOf(Passenger::class);

    $savedPassenger->setFirstName('Jane');

    $entityManager->flush();

    expect($savedPassenger->getFirstName())
        ->toBe('Jane');

    $entityManager->remove($passenger);
    $entityManager->flush();
});

it('deletes a passenger', function () {
    $container = static::getContainer();

    $entityManager = $container->get(EntityManagerInterface::class);
    $repository = $container->get(PassengerRepository::class);

    $passenger = new Passenger();
    $passenger->setFirstName('Juan');
    $passenger->setLastName('Dela Cruz');
    $passenger->setEmail('some@email.com');

    $entityManager->persist($passenger);
    $entityManager->flush();

    $passengerId = $passenger->getId();
    $savedPassenger = $repository->find($passengerId);
    $entityManager->remove($savedPassenger);
    $entityManager->flush();

    expect($repository->find($passengerId))->toBeNull();
});