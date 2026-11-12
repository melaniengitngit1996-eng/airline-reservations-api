<?php

use App\Controller\Airport\UpdateAirportController;
use App\Entity\Airport;
use App\Repository\AirportRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\ObjectMapper\ObjectMapperInterface;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

it('has update airport API endpoint', function () {
    $client = $this->createClient();

    $client->jsonRequest('PATCH', '/api/airports/1', [
        'code' => 'ABC',
        'name' => 'Some Airport',
        'city' => 'Some City',
        'country' => 'Philippines',
    ]);

    expect($client->getResponse()->getStatusCode())
        ->not->toBe(404);

    expect($client->getResponse()->getStatusCode())
        ->not->toBe(405);
});

it('updates airport with missing fields. (400)', function () {
    $client = $this->createClient();

    $client->jsonRequest('PATCH', '/api/airports/1', []);

    expect($client->getResponse()->getStatusCode())
        ->toBe(400);

    $response = json_decode(
        $client->getResponse()->getContent(),
        true
    );

    expect($response['message'])
        ->toBe('Required fields are missing.');
});

it('updates airport with duplicate code. (409)', function () {
    $client = $this->createClient();

    $entityManager = $this->createMock(EntityManagerInterface::class);

    $existingAirport = new Airport();
    $existingAirport->setCode('ABC');
    $existingAirport->setName('Existing Airport');
    $existingAirport->setCity('Manila');
    $existingAirport->setCountry('Philippines');

    $airport = new Airport();
    $airport->setCode('XYZ');
    $airport->setName('Some Airport');
    $airport->setCity('Some City');
    $airport->setCountry('Philippines');

    $entityManager->persist($existingAirport);
    $entityManager->persist($airport);
    $entityManager->flush();

    $client->jsonRequest('PATCH', '/api/airports/1', [
        'code' => 'ABC',
    ]);

    expect($client->getResponse()->getStatusCode())
        ->toBe(409);

    $response = json_decode(
        $client->getResponse()->getContent(),
        true
    );

    expect($response['message'])
        ->toBe('Airport code already exists.');
});

it('updates airport successfully. (200)', function () {
    $client = $this->createClient();

    $entityManager = $this->createMock(EntityManagerInterface::class);
    $validator = $this->createMock(ValidatorInterface::class);
    $objectMapper = $this->createMock(ObjectMapperInterface::class);

    $airport = new Airport();
    $airport->setCode('ABC');
    $airport->setName('Some Airport');
    $airport->setCity('Some City');
    $airport->setCountry('Philippines');

    $airportRepository = $this->createMock(AirportRepository::class);

    $airportRepository
        ->expects($this->once())
        ->method('find')
        ->with(1)
        ->willReturn($airport);

    $validator
        ->expects($this->once())
        ->method('validate')
        ->with($airport)
        ->willReturn(new ConstraintViolationList());

    $entityManager
        ->method('getRepository')
        ->with(Airport::class)
        ->willReturn($airportRepository);

    $entityManager
        ->expects($this->once())
        ->method('flush');

    static::getContainer()->set(
        UpdateAirportController::class,
        new UpdateAirportController(
            $entityManager,
            $validator,
            $objectMapper
        )
    );

    $client->jsonRequest('PATCH', '/api/airports/1', [
        'code' => 'XYZ',
        'name' => 'Updated Airport',
        'city' => 'Updated City',
        'country' => 'Philippines',
    ]);

    expect($client->getResponse()->getStatusCode())
        ->toBe(200);

    $response = json_decode(
        $client->getResponse()->getContent(),
        true
    );

    expect($response['code'])
        ->toBe('XYZ');
});