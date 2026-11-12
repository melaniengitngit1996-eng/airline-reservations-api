<?php

use App\Controller\Airport\GetAirportController;
use App\Entity\Airport;
use App\Repository\AirportRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\ObjectMapper\ObjectMapperInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

it('has get airport API endpoint', function () {
    $client = $this->createClient();

    $client->jsonRequest('GET', '/api/airports/1');

    expect($client->getResponse()->getStatusCode())
        ->not->toBe(404);

    expect($client->getResponse()->getStatusCode())
        ->not->toBe(405);
});

it('gets unknown airport (404)', function () {
    $client = $this->createClient();

    $entityManager = $this->createMock(EntityManagerInterface::class);
    $validator = $this->createMock(ValidatorInterface::class);
    $objectMapper = $this->createMock(ObjectMapperInterface::class);

    $airportRepository = $this->createMock(AirportRepository::class);

    $airportRepository
        ->expects($this->once())
        ->method('find')
        ->with(1)
        ->willReturn(null);

    $entityManager
        ->method('getRepository')
        ->with(Airport::class)
        ->willReturn($airportRepository);

    static::getContainer()->set(
        GetAirportController::class,
        new GetAirportController(
            $entityManager,
            $validator,
            $objectMapper
        )
    );

    $client->jsonRequest('GET', '/api/airports/1');

    expect($client->getResponse()->getStatusCode())
        ->toBe(404);

    $response = json_decode(
        $client->getResponse()->getContent(),
        true
    );

    expect($response['message'])
        ->toBe('Airport not found.');
});

it('gets airport successfully (200)', function () {
    $client = $this->createClient();

    $entityManager = $this->createMock(EntityManagerInterface::class);
    $validator = $this->createMock(ValidatorInterface::class);
    $objectMapper = $this->createMock(ObjectMapperInterface::class);

    $airport = new Airport();
    $airport->setCode('MNL');
    $airport->setName('Ninoy Aquino International Airport');
    $airport->setCity('Manila');
    $airport->setCountry('Philippines');

    $airportRepository = $this->createMock(AirportRepository::class);

    $airportRepository
        ->expects($this->once())
        ->method('find')
        ->with(1)
        ->willReturn($airport);

    $entityManager
        ->method('getRepository')
        ->with(Airport::class)
        ->willReturn($airportRepository);

    static::getContainer()->set(
        GetAirportController::class,
        new GetAirportController(
            $entityManager,
            $validator,
            $objectMapper
        )
    );

    $client->jsonRequest('GET', '/api/airports/1');

    expect($client->getResponse()->getStatusCode())
        ->toBe(200);

    $response = json_decode(
        $client->getResponse()->getContent(),
        true
    );

    expect($response['code'])
        ->toBe('MNL');
});