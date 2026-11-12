<?php

use App\Controller\Reservation\DeleteReservationController;
use App\Entity\Reservation;
use App\Repository\ReservationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\ObjectMapper\ObjectMapperInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

it('has delete reservation API endpoint.', function () {
    $client = $this->createClient();

    $entityManager = $this->createMock(EntityManagerInterface::class);
    $validator = $this->createMock(ValidatorInterface::class);
    $objectMapper = $this->createMock(ObjectMapperInterface::class);

    $reservation = new Reservation();
    $reservationRepository = $this->createMock(ReservationRepository::class);
    
    $entityManager->method('getRepository')->willReturn($reservationRepository);

    $reservationRepository
        ->expects($this->once())
        ->method('find')
        ->with(1)
        ->willReturn($reservation);

    $entityManager->expects($this->once())->method('remove');

    $entityManager->expects($this->once())->method('flush');

    static::getContainer()->set(
        DeleteReservationController::class,
        new DeleteReservationController(
            $entityManager,
            $validator,
            $objectMapper
        )
    );

    $client->jsonRequest('DELETE', '/api/reservations/1');

    expect($client->getResponse()->getStatusCode())
        ->not->toBe(404);

    expect($client->getResponse()->getStatusCode())
        ->not->toBe(405);
});

it('deletes unknown reservation (404)', function () {
    $client = $this->createClient();

    $client->jsonRequest('DELETE', '/api/reservations/100');

    expect($client->getResponse()->getStatusCode())
        ->toBe(404);

    $response = json_decode(
        $client->getResponse()->getContent(),
        true
    );

    expect($response['message'])
        ->toBe('Reservation not found.');
});

it('deletes reservation successfully (200)', function () {
    $client = $this->createClient();

    $entityManager = $this->createMock(EntityManagerInterface::class);
    $validator = $this->createMock(ValidatorInterface::class);
    $objectMapper = $this->createMock(ObjectMapperInterface::class);

    $reservation = new Reservation();
    $reservationRepository = $this->createMock(ReservationRepository::class);
    $reservationRepository
        ->expects($this->once())
        ->method('find')
        ->with(1)
        ->willReturn($reservation);

    $entityManager->method('getRepository')->willReturn($reservationRepository);

    $entityManager->expects($this->once())->method('remove');

    $entityManager->expects($this->once())->method('flush');

    static::getContainer()->set(
        DeleteReservationController::class,
        new DeleteReservationController(
            $entityManager,
            $validator,
            $objectMapper
        )
    );

    $client->jsonRequest('DELETE', '/api/reservations/1');

    expect($client->getResponse()->getStatusCode())
        ->toBe(204);
});