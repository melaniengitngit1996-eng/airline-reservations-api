<?php

use App\Controller\Airport\GetAllAirportsController;
use App\Entity\Airport;
use App\Repository\AirportRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\ObjectMapper\ObjectMapperInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

it('has get all airports API endpoint', function () {
    $client = $this->createClient();

    $client->jsonRequest('GET', '/api/airports');

    expect($client->getResponse()->getStatusCode())
        ->not->toBe(404);

    expect($client->getResponse()->getStatusCode())
        ->not->toBe(405);
});

it('gets all airports successfully. (200)', function () {
    $client = $this->createClient();

    $entityManager = $this->createMock(EntityManagerInterface::class);
    $validator = $this->createMock(ValidatorInterface::class);
    $objectMapper = $this->createMock(ObjectMapperInterface::class);

    $airport1 = new Airport();
    $airport1->setCode('MNL');
    $airport1->setName('Ninoy Aquino International Airport');
    $airport1->setCity('Manila');
    $airport1->setCountry('Philippines');

    $airport2 = new Airport();
    $airport2->setCode('CEB');
    $airport2->setName('Mactan-Cebu International Airport');
    $airport2->setCity('Cebu');
    $airport2->setCountry('Philippines');

    $airportRepository = $this->createMock(AirportRepository::class);

    $airportRepository
        ->expects($this->once())
        ->method('findAll')
        ->willReturn([
            $airport1,
            $airport2,
        ]);

    $entityManager
        ->method('getRepository')
        ->with(Airport::class)
        ->willReturn($airportRepository);

    static::getContainer()->set(
        GetAllAirportsController::class,
        new GetAllAirportsController(
            $entityManager,
            $validator,
            $objectMapper
        )
    );

    $client->jsonRequest('GET', '/api/airports');

    expect($client->getResponse()->getStatusCode())
        ->toBe(200);

    $response = json_decode(
        $client->getResponse()->getContent(),
        true
    );

    expect($response)
        ->toHaveCount(2);

    expect($response[0]['code'])
        ->toBe('MNL');

    expect($response[1]['code'])
        ->toBe('CEB');
});

it('gets airports filtered by code. (200)', function () {
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
        ->method('findBy')
        ->with([
            'code' => 'MNL',
        ])
        ->willReturn([$airport]);

    $entityManager
        ->method('getRepository')
        ->with(Airport::class)
        ->willReturn($airportRepository);

    static::getContainer()->set(
        GetAllAirportsController::class,
        new GetAllAirportsController(
            $entityManager,
            $validator,
            $objectMapper
        )
    );

    $client->jsonRequest('GET', '/api/airports?code=MNL');

    expect($client->getResponse()->getStatusCode())
        ->toBe(200);

    $response = json_decode(
        $client->getResponse()->getContent(),
        true
    );

    expect($response)
        ->toHaveCount(1);

    expect($response[0]['code'])
        ->toBe('MNL');
});

it('gets airports filtered by name. (200)', function () {
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
        ->method('findBy')
        ->with([
            'name' => 'Ninoy Aquino International Airport',
        ])
        ->willReturn([$airport]);

    $entityManager
        ->method('getRepository')
        ->with(Airport::class)
        ->willReturn($airportRepository);

    static::getContainer()->set(
        GetAllAirportsController::class,
        new GetAllAirportsController(
            $entityManager,
            $validator,
            $objectMapper
        )
    );

    $client->jsonRequest(
        'GET',
        '/api/airports?name=Ninoy%20Aquino%20International%20Airport'
    );

    expect($client->getResponse()->getStatusCode())
        ->toBe(200);

    $response = json_decode(
        $client->getResponse()->getContent(),
        true
    );

    expect($response)
        ->toHaveCount(1);

    expect($response[0]['name'])
        ->toBe('Ninoy Aquino International Airport');
});

it('gets no airports when filter does not match. (200)', function () {
    $client = $this->createClient();

    $entityManager = $this->createMock(EntityManagerInterface::class);
    $validator = $this->createMock(ValidatorInterface::class);
    $objectMapper = $this->createMock(ObjectMapperInterface::class);

    $airportRepository = $this->createMock(AirportRepository::class);

    $airportRepository
        ->expects($this->once())
        ->method('findBy')
        ->with([
            'code' => 'XXX',
        ])
        ->willReturn([]);

    $entityManager
        ->method('getRepository')
        ->with(Airport::class)
        ->willReturn($airportRepository);

    static::getContainer()->set(
        GetAllAirportsController::class,
        new GetAllAirportsController(
            $entityManager,
            $validator,
            $objectMapper
        )
    );

    $client->jsonRequest('GET', '/api/airports?code=XXX');

    expect($client->getResponse()->getStatusCode())
        ->toBe(200);

    $response = json_decode(
        $client->getResponse()->getContent(),
        true
    );

    expect($response)
        ->toHaveCount(0);
});