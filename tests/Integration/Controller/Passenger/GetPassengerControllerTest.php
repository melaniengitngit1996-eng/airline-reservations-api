<?php

use App\Controller\Passenger\GetPassengerController;
use App\Entity\Passenger;
use App\Repository\PassengerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\ObjectMapper\ObjectMapperInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

it('has get passenger API endpoint', function () {
    $client = $this->createClient();

    $client->jsonRequest('GET', '/api/passengers/1');

    expect($client->getResponse()->getStatusCode())
        ->not->toBe(404);
});

it('gets unknown passenger (404)', function() {
    $client = $this->createClient();

    $client->jsonRequest('GET', '/api/passengers/200');

    expect($client->getResponse()->getStatusCode())
        ->toBe(404);

    $response = json_decode($client->getResponse()->getContent(), true);

    expect($response['message'])->toBe('Passenger not found.');
});

it('gets passenger successfully (200)', function() {
    $client = $this->createClient();

    $entityManager = $this->createMock(EntityManagerInterface::class);
    $validator = $this->createMock(ValidatorInterface::class);
    $objectMapper = $this->createMock(ObjectMapperInterface::class);

    $passengerRepository = $this->createMock(PassengerRepository::class);

    $entityManager
        ->expects($this->once())
        ->method('getRepository')
        ->willReturn($passengerRepository);

    $passenger = new Passenger();
    $passenger->setFirstName('Jane');
    $passenger->setLastName('Doe');

    $passengerRepository
        ->expects($this->once())
        ->method('find')
        ->with(1)
        ->willReturn($passenger);

    static::getContainer()->set(
        GetPassengerController::class,
        new GetPassengerController(
            $entityManager,
            $validator,
            $objectMapper
        )
    );

    $client->jsonRequest('GET', '/api/passengers/1');

    expect($client->getResponse()->getStatusCode())
        ->toBe(200);

    $response = json_decode($client->getResponse()->getContent(), true);

    expect($response['first_name'])->toBe('Jane');
});