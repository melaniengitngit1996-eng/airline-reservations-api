<?php

use App\Controller\Airport\CreateAirportController;
use App\Entity\Airport;
use App\Entity\Flight;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\ObjectMapper\ObjectMapperInterface;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

it('has create airport API endpoint', function () {
    $client = $this->createClient();

    $client->jsonRequest('POST', '/api/airports', []);

    expect($client->getResponse()->getStatusCode())
        ->not->toBe(404);
});

it('creates an airport with duplicate code (409)', function() {
    $client = $this->createClient();
    $client->jsonRequest('POST', '/api/airports', [
        'code' => 'ABC',
        'name' => 'Some Airport',
        'city' => 'Some City',
        'country' => 'Philippines',
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

it('creates an airport with blank fields (400)', function() {
    $client = $this->createClient();
    $client->jsonRequest('POST', '/api/airports', [
        'code' => 'ABC',
        'name' => 'Some Airport',
        'city' => 'Some City',
        'country' => '',
    ]);

    expect($client->getResponse()->getStatusCode())
        ->toBe(400);

    $response = json_decode(
        $client->getResponse()->getContent(),
        true
    );

    expect($response['message'])
        ->toBe('Required fields are missing.');
});

it('creates an airport with missing fields (400)', function() {
    $client = $this->createClient();
    $client->jsonRequest('POST', '/api/airports', [
        'code' => 'ABC',
        'name' => 'Some Airport',
        'city' => 'Some City',
    ]);

    expect($client->getResponse()->getStatusCode())
        ->toBe(400);

    $response = json_decode(
        $client->getResponse()->getContent(),
        true
    );

    expect($response['message'])
        ->toBe('Required fields are missing.');
});

it('creates an airport with empty fields (400)', function() {
    $client = $this->createClient();
    $client->jsonRequest('POST', '/api/airports', []);

    expect($client->getResponse()->getStatusCode())
        ->toBe(400);

    $response = json_decode(
        $client->getResponse()->getContent(),
        true
    );

    expect($response['message'])
        ->toBe('Required fields are missing.');
});

it('creates a airport successfully (201)', function () {
    $client = $this->createClient();

    $entityManager = $this->createMock(EntityManagerInterface::class);
    $validator = $this->createMock(ValidatorInterface::class);
    $objectMapper = $this->createMock(ObjectMapperInterface::class);
    $airport = new Airport();
    $airport->setCode('ABC');
    $airport->setName('Some Airport');
    $airport->setCity('Some City');
    $airport->setCountry('Philippines');

    $validator->expects($this->exactly(2))->method('validate')->willReturn(new ConstraintViolationList());

    $objectMapper->expects($this->once())->method('map')->willReturn($airport);

    $entityManager->expects($this->once())->method('persist');
    $entityManager->expects($this->once())->method('flush');

    static::getContainer()->set(
        CreateAirportController::class,
        new CreateAirportController(
            $entityManager,
            $validator,
            $objectMapper
        )
    );

    $client->jsonRequest('POST', '/api/airports', [
        'code' => 'ABC',
        'name' => 'Some Airport',
        'city' => 'Some City',
        'country' => 'Philippines',
    ]);

    expect($client->getResponse()->getStatusCode())
        ->toBe(201);

    $response = json_decode(
        $client->getResponse()->getContent(),
        true
    );

    expect($response['code'])->toBe('ABC');
});