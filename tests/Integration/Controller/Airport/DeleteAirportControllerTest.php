<?php

use App\Controller\Airport\DeleteAirportController;
use App\Entity\Airport;
use App\Entity\Flight;
use App\Repository\AirportRepository;
use App\Repository\FlightRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\ObjectMapper\ObjectMapperInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

it('has delete airport API endpoint.', function () {
    $client = $this->createClient();

    $client->jsonRequest('DELETE', '/api/airports/1');

    expect($client->getResponse()->getStatusCode())
        ->not->toBe(405);
});

it('deletes unknown airport (404)', function () {
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
        DeleteAirportController::class,
        new DeleteAirportController(
            $entityManager,
            $validator,
            $objectMapper
        )
    );

    $client->jsonRequest('DELETE', '/api/airports/1');

    expect($client->getResponse()->getStatusCode())
        ->toBe(404);

    $response = json_decode(
        $client->getResponse()->getContent(),
        true
    );

    expect($response['message'])
        ->toBe('Airport not found.');
});

it('deletes airport with flights (409)', function () {
    $client = $this->createClient();

    $entityManager = $this->createMock(EntityManagerInterface::class);
    $validator = $this->createMock(ValidatorInterface::class);
    $objectMapper = $this->createMock(ObjectMapperInterface::class);

    $airport = new Airport();
    $airport->setCode('ABC');
    $airport->setName('Some Airport');
    $airport->setCity('Some City');
    $airport->setCountry('Philippines');

    $flight = new Flight();

    $airportRepository = $this->createMock(AirportRepository::class);
    $airportRepository
        ->expects($this->once())
        ->method('find')
        ->with(1)
        ->willReturn($airport);

    $flightRepository = $this->createMock(FlightRepository::class);
    $flightRepository
        ->expects($this->once())
        ->method('findBy')
        ->with([
            'originAirport' => $airport,
        ])
        ->willReturn([$flight]);

    $entityManager
        ->method('getRepository')
        ->willReturnMap([
            [Airport::class, $airportRepository],
            [Flight::class, $flightRepository],
        ]);

    static::getContainer()->set(
        DeleteAirportController::class,
        new DeleteAirportController(
            $entityManager,
            $validator,
            $objectMapper
        )
    );

    $client->jsonRequest('DELETE', '/api/airports/1');

    expect($client->getResponse()->getStatusCode())
        ->toBe(409);

    $response = json_decode(
        $client->getResponse()->getContent(),
        true
    );

    expect($response['message'])
        ->toBe('This airport has existing flights.');
});

it('successfully deletes airport (204)', function () {
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

    $flightRepository = $this->createMock(FlightRepository::class);

    $flightRepository
        ->expects($this->once())
        ->method('findBy')
        ->with([
            'originAirport' => $airport,
        ])
        ->willReturn([]);

    $entityManager
        ->method('getRepository')
        ->willReturnMap([
            [Airport::class, $airportRepository],
            [Flight::class, $flightRepository],
        ]);

    $entityManager
        ->expects($this->once())
        ->method('remove')
        ->with($airport);

    $entityManager
        ->expects($this->once())
        ->method('flush');

    static::getContainer()->set(
        DeleteAirportController::class,
        new DeleteAirportController(
            $entityManager,
            $validator,
            $objectMapper
        )
    );

    $client->jsonRequest('DELETE', '/api/airports/1');

    expect($client->getResponse()->getStatusCode())
        ->toBe(204);
});