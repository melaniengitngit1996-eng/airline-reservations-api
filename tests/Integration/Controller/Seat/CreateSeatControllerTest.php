<?php

use App\Controller\Seat\CreateSeatController;
use App\Entity\Flight;
use App\Repository\FlightRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Seat;
use App\Repository\SeatRepository;
use Symfony\Component\ObjectMapper\ObjectMapperInterface;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

it('has create seat API endpoint', function () {
    $client = $this->createClient();

    $client->jsonRequest('POST', '/api/flights/1/seats', []);

    expect($client->getResponse()->getStatusCode())
        ->not->toBe(404);
});

it('creates seat in an unknown flight (404)', function () {
    $client = $this->createClient();
    $client->jsonRequest('POST', '/api/flights/7/seats', [
        'code' => 'A2',
    ]);

    expect($client->getResponse()->getStatusCode())
        ->toBe(404);

    $response = json_decode(
        $client->getResponse()->getContent(),
        true
    );

    expect($response['message'])
        ->toBe('Flight not found.');
});

it('creates seat with duplicate seat code in a flight (409)', function () {
    $client = $this->createClient();
    $client->jsonRequest('POST', '/api/flights/1/seats', [
        'code' => '1A',
    ]);

    expect($client->getResponse()->getStatusCode())
        ->toBe(409);

    $response = json_decode(
        $client->getResponse()->getContent(),
        true
    );

    expect($response['message'])
        ->toBe('The seat code already exists for this flight.');
});

it('creates seat with blank field (400)', function () {
    $client = $this->createClient();
    $client->jsonRequest('POST', '/api/flights/1/seats', [
        'code' => '',
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

it('creates seat with empty fields (400)', function () {
    $client = $this->createClient();
    $client->jsonRequest('POST', '/api/flights/1/seats', []);

    expect($client->getResponse()->getStatusCode())
        ->toBe(400);

    $response = json_decode(
        $client->getResponse()->getContent(),
        true
    );

    expect($response['message'])
        ->toBe('Required fields are missing.');
});

it('creates seat successfully (201)', function () {
    $client = $this->createClient();

    $flight = new Flight();
    $seat = new Seat();
    $seat->setFlight($flight);
    $seat->setCode('A1');

    // find flight
    $flightRepository = $this->createMock(FlightRepository::class);
    $flightRepository->method('find')->willReturn($flight);

    // get repo for flight
    $entityManager = $this->createMock(EntityManagerInterface::class);
    $entityManager->method('getRepository')->willReturn($flightRepository);

    // validation dto
    $validator = $this->createMock(ValidatorInterface::class);
    $validator->method('validate')->willReturn(new ConstraintViolationList());

    // map dto to class seat
    $objectMapper = $this->createMock(ObjectMapperInterface::class);
    $objectMapper->method('map')->willReturn($seat);

    static::getContainer()->set(
        CreateSeatController::class,
        new CreateSeatController(
            $entityManager,
            $validator,
            $objectMapper
        )
    );

    $client->jsonRequest('POST', '/api/flights/1/seats', [
        'code' => 'A1'
    ]);

    expect($client->getResponse()->getStatusCode())->toBe(201);
});