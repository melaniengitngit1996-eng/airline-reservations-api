<?php

use App\Entity\Passenger;
use App\Repository\PassengerRepository;
use App\Service\PassengerService;
use Doctrine\ORM\EntityManagerInterface;

it('creates a passenger', function () {
    $repository = $this->createStub(PassengerRepository::class);
    $entityManager = $this->createMock(EntityManagerInterface::class);

    $entityManager->expects($this->once())
        ->method('persist');

    $entityManager->expects($this->once())
        ->method('flush');

    $service = new PassengerService($repository, $entityManager);

    $passenger = $service->create([
        'first_name' => 'Juan',
        'last_name' => 'Dela Cruz',
        'email' => 'juan@example.com',
    ]);

    expect($passenger)->toBeInstanceOf(Passenger::class);
});

it('gets all passengers', function () {
    $passenger = new Passenger();

    $mock = $this->createMock(PassengerRepository::class);

    $mock->expects($this->once())
        ->method('findByFilters')
        ->with(null, null)
        ->willReturn([$passenger]);

    $entityManager = $this->createMock(EntityManagerInterface::class);

    $service = new PassengerService($mock, $entityManager);

    $passengers = $service->findAll();

    expect($passengers)->toHaveCount(1);
    expect($passengers[0])->toBeInstanceOf(Passenger::class);
});

it('gets passengers by first name', function () {
    $passenger = new Passenger();
    $passenger->setFirstName('Juan');

    $mock = $this->createMock(PassengerRepository::class);

    $mock->expects($this->once())
        ->method('findByFilters')
        ->with('Juan', null)
        ->willReturn([$passenger]);

    $entityManager = $this->createMock(EntityManagerInterface::class);

    $service = new PassengerService($mock, $entityManager);

    $passengers = $service->findAll('Juan');

    expect($passengers)->toHaveCount(1);
    expect($passengers[0])->toBe($passenger);
});

it('gets passengers by last name', function () {
    $passenger = new Passenger();
    $passenger->setLastName('Dela Cruz');

    $mock = $this->createMock(PassengerRepository::class);

    $mock->expects($this->once())
        ->method('findByFilters')
        ->with(null, 'Dela Cruz')
        ->willReturn([$passenger]);

    $entityManager = $this->createMock(EntityManagerInterface::class);

    $service = new PassengerService($mock, $entityManager);

    $passengers = $service->findAll(null, 'Dela Cruz');

    expect($passengers)->toHaveCount(1);
    expect($passengers[0])->toBe($passenger);
});

it('gets passengers by first name and last name', function () {
    $passenger = new Passenger();
    $passenger->setFirstName('Juan');
    $passenger->setLastName('Dela Cruz');

    $mock = $this->createMock(PassengerRepository::class);

    $mock->expects($this->once())
        ->method('findByFilters')
        ->with('Juan', 'Dela Cruz')
        ->willReturn([$passenger]);

    $entityManager = $this->createMock(EntityManagerInterface::class);

    $service = new PassengerService($mock, $entityManager);

    $passengers = $service->findAll('Juan', 'Dela Cruz');

    expect($passengers)->toHaveCount(1);
    expect($passengers[0])->toBe($passenger);
});

it('get by passenger id', function () {
    $passenger = new Passenger();

    $mock = $this->createMock(PassengerRepository::class);

    $mock->expects($this->once())
        ->method('find')
        ->with(1)
        ->willReturn($passenger);

    $entityManager = $this->createMock(EntityManagerInterface::class);

    $service = new PassengerService($mock, $entityManager);

    $passenger = $service->find(1);

    expect($passenger)->toBeInstanceOf(Passenger::class);
});

it('updates a passenger', function () {
    $passenger = new Passenger();
    $passenger->setFirstName('Juan');
    $passenger->setLastName('Dela Cruz');
    $passenger->setEmail('juan@example.com');

    $repository = $this->createStub(PassengerRepository::class);

    $repository->method('find')
        ->willReturn($passenger);

    $entityManager = $this->createMock(EntityManagerInterface::class);

    $entityManager->expects($this->once())
        ->method('flush');

    $service = new PassengerService($repository, $entityManager);

    $result = $service->update(1, [
        'first_name' => 'Juan 2',
        'last_name' => 'Dela Cruz',
        'email' => 'juan@example.com',
    ]);

    expect($result->getFirstName())->toBe('Juan 2');
});

it('deletes a passenger', function () {
    $passenger = new Passenger();

    $repository = $this->createStub(PassengerRepository::class);

    $repository->method('find')
        ->willReturn($passenger);

    $entityManager = $this->createMock(EntityManagerInterface::class);

    $entityManager->expects($this->once())
        ->method('remove')
        ->with($passenger);

    $entityManager->expects($this->once())
        ->method('flush');

    $service = new PassengerService($repository, $entityManager);

    $result = $service->delete(1);

    expect($result)->toBeTrue();
});