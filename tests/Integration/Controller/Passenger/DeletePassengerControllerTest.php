<?php

use App\Controller\Passenger\DeletePassengerController;
use App\Entity\Passenger;
use App\Repository\PassengerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\ObjectMapper\ObjectMapperInterface;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

it('has delete passenger API endpoint. (200)', function () {
    $client = $this->createClient();

    $entityManager = $this->createMock(EntityManagerInterface::class);
    $validator = $this->createMock(ValidatorInterface::class);
    $objectMapper = $this->createMock(ObjectMapperInterface::class);

    $passenger = new Passenger();

    $passengerRepository = $this->createMock(PassengerRepository::class);

    $passengerRepository
        ->expects($this->once())
        ->method('find')
        ->with(1)
        ->willReturn($passenger);

    $entityManager
        ->method('getRepository')
        ->willReturnMap([
            [Passenger::class, $passengerRepository],
        ]);

    $validator
        ->method('validate')
        ->willReturn(new ConstraintViolationList());

    $entityManager->expects($this->once())->method('flush');

    static::getContainer()->set(
        DeletePassengerController::class,
        new DeletePassengerController(
            $entityManager,
            $validator,
            $objectMapper
        )
    );

    $client->jsonRequest('DELETE', '/api/passengers/1');

    expect($client->getResponse()->getStatusCode())
        ->not->toBe(404);

    expect($client->getResponse()->getStatusCode())
        ->not->toBe(405);
});

it('deletes unknown passenger (404)', function () {
    $client = $this->createClient();

    $entityManager = $this->createMock(EntityManagerInterface::class);
    $validator = $this->createMock(ValidatorInterface::class);
    $objectMapper = $this->createMock(ObjectMapperInterface::class);

    $passengerRepository = $this->createMock(PassengerRepository::class);

    $passengerRepository
        ->expects($this->once())
        ->method('find')
        ->with(100)
        ->willReturn(null);

    $entityManager
        ->method('getRepository')
        ->willReturnMap([
            [Passenger::class, $passengerRepository],
        ]);

    static::getContainer()->set(
        DeletePassengerController::class,
        new DeletePassengerController(
            $entityManager,
            $validator,
            $objectMapper
        )
    );

    $client->jsonRequest('DELETE', '/api/passengers/100');

    expect($client->getResponse()->getStatusCode())
        ->toBe(404);

    $response = json_decode(
        $client->getResponse()->getContent(),
        true
    );

    expect($response['message'])
        ->toBe('Passenger not found.');
});

it('has deletes passenger successfully. (204)', function () {
    $client = $this->createClient();

    $entityManager = $this->createMock(EntityManagerInterface::class);
    $validator = $this->createMock(ValidatorInterface::class);
    $objectMapper = $this->createMock(ObjectMapperInterface::class);

    $passenger = new Passenger();

    $passengerRepository = $this->createMock(PassengerRepository::class);

    $passengerRepository
        ->expects($this->once())
        ->method('find')
        ->with(1)
        ->willReturn($passenger);

    $entityManager
        ->method('getRepository')
        ->willReturnMap([
            [Passenger::class, $passengerRepository],
        ]);

    $validator
        ->method('validate')
        ->willReturn(new ConstraintViolationList());

    $entityManager->expects($this->once())->method('flush');

    static::getContainer()->set(
        DeletePassengerController::class,
        new DeletePassengerController(
            $entityManager,
            $validator,
            $objectMapper
        )
    );

    $client->jsonRequest('DELETE', '/api/passengers/1');

    expect($client->getResponse()->getStatusCode())
        ->toBe(204);
});