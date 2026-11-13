<?php

use App\Controller\Passenger\GetAllPassengersController;
use App\Entity\Passenger;
use App\Repository\PassengerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\ObjectMapper\ObjectMapperInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

it('gets all passengers. (200)', function () {
    $client = $this->createClient();

    $entityManager = $this->createMock(EntityManagerInterface::class);
    $validator = $this->createMock(ValidatorInterface::class);
    $objectMapper = $this->createMock(ObjectMapperInterface::class);

    static::getContainer()->set(
        GetAllPassengersController::class,
        new GetAllPassengersController(
            $entityManager,
            $validator,
            $objectMapper
        )
    );

    $passenger1 = $this->createMock(Passenger::class);
    $passenger1->method('getId')->willReturn(1);
    $passenger1->method('getFirstName')->willReturn('Juan');
    $passenger1->method('getLastName')->willReturn('Dela Cruz');
    $passenger1->method('getEmail')->willReturn('juan@example.com');

    $passenger2 = $this->createMock(Passenger::class);
    $passenger2->method('getId')->willReturn(2);
    $passenger2->method('getFirstName')->willReturn('Maria');
    $passenger2->method('getLastName')->willReturn('Santos');
    $passenger2->method('getEmail')->willReturn('maria@example.com');

    $passengerRepository = $this->createMock(PassengerRepository::class);

    $passengerRepository
        ->method('findAll')
        ->willReturn([$passenger1, $passenger2]);

    $entityManager
        ->method('getRepository')
        ->with(Passenger::class)
        ->willReturn($passengerRepository);

    $client->request('GET', '/api/passengers');

    expect($client->getResponse()->getStatusCode())->toBe(200)
        ->and(json_decode($client->getResponse()->getContent(), true))->toBe([
            [
                'id' => 1,
                'first_name' => 'Juan',
                'last_name' => 'Dela Cruz',
                'email' => 'juan@example.com',
            ],
            [
                'id' => 2,
                'first_name' => 'Maria',
                'last_name' => 'Santos',
                'email' => 'maria@example.com',
            ],
        ]);
});


it('gets passengers by first name. (200)', function () {
    $client = $this->createClient();

    $entityManager = $this->createMock(EntityManagerInterface::class);
    $validator = $this->createMock(ValidatorInterface::class);
    $objectMapper = $this->createMock(ObjectMapperInterface::class);

    static::getContainer()->set(
        GetAllPassengersController::class,
        new GetAllPassengersController(
            $entityManager,
            $validator,
            $objectMapper
        )
    );

    $passenger = $this->createMock(Passenger::class);
    $passenger->method('getId')->willReturn(1);
    $passenger->method('getFirstName')->willReturn('Juan');
    $passenger->method('getLastName')->willReturn('Dela Cruz');
    $passenger->method('getEmail')->willReturn('juan@example.com');

    $passengerRepository = $this->createMock(PassengerRepository::class);

    $passengerRepository
        ->method('findBy')
        ->with([
            'firstName' => 'Juan',
        ])
        ->willReturn([$passenger]);

    $entityManager
        ->method('getRepository')
        ->with(Passenger::class)
        ->willReturn($passengerRepository);

    $client->request('GET', '/api/passengers?first_name=Juan');

    expect($client->getResponse()->getStatusCode())->toBe(200)
        ->and(json_decode($client->getResponse()->getContent(), true))->toBe([
            [
                'id' => 1,
                'first_name' => 'Juan',
                'last_name' => 'Dela Cruz',
                'email' => 'juan@example.com',
            ],
        ]);
});


it('gets passengers by last name. (200)', function () {
    $client = $this->createClient();

    $entityManager = $this->createMock(EntityManagerInterface::class);
    $validator = $this->createMock(ValidatorInterface::class);
    $objectMapper = $this->createMock(ObjectMapperInterface::class);

    static::getContainer()->set(
        GetAllPassengersController::class,
        new GetAllPassengersController(
            $entityManager,
            $validator,
            $objectMapper
        )
    );

    $passenger = $this->createMock(Passenger::class);
    $passenger->method('getId')->willReturn(2);
    $passenger->method('getFirstName')->willReturn('Maria');
    $passenger->method('getLastName')->willReturn('Santos');
    $passenger->method('getEmail')->willReturn('maria@example.com');

    $passengerRepository = $this->createMock(PassengerRepository::class);

    $passengerRepository
        ->method('findBy')
        ->with([
            'lastName' => 'Santos',
        ])
        ->willReturn([$passenger]);

    $entityManager
        ->method('getRepository')
        ->with(Passenger::class)
        ->willReturn($passengerRepository);

    $client->request('GET', '/api/passengers?last_name=Santos');

    expect($client->getResponse()->getStatusCode())->toBe(200)
        ->and(json_decode($client->getResponse()->getContent(), true))->toBe([
            [
                'id' => 2,
                'first_name' => 'Maria',
                'last_name' => 'Santos',
                'email' => 'maria@example.com',
            ],
        ]);
});


it('gets empty passengers list. (200)', function () {
    $client = $this->createClient();

    $entityManager = $this->createMock(EntityManagerInterface::class);
    $validator = $this->createMock(ValidatorInterface::class);
    $objectMapper = $this->createMock(ObjectMapperInterface::class);

    static::getContainer()->set(
        GetAllPassengersController::class,
        new GetAllPassengersController(
            $entityManager,
            $validator,
            $objectMapper
        )
    );

    $passengerRepository = $this->createMock(PassengerRepository::class);

    $passengerRepository
        ->method('findAll')
        ->willReturn([]);

    $entityManager
        ->method('getRepository')
        ->with(Passenger::class)
        ->willReturn($passengerRepository);

    $client->request('GET', '/api/passengers');

    expect($client->getResponse()->getStatusCode())->toBe(200)
        ->and(json_decode($client->getResponse()->getContent(), true))->toBe([]);
});