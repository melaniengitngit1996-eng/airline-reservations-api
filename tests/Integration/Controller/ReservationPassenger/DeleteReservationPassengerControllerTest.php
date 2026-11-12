<?php

use App\Controller\ReservationPassenger\DeleteReservationPassengerController;
use App\Entity\Passenger;
use App\Entity\Reservation;
use App\Entity\ReservationPassenger;
use App\Repository\ReservationPassengerRepository;
use App\Repository\ReservationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\ObjectMapper\ObjectMapperInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

it('has delete passenger API endpoint', function () {
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

    $reservationPassenger = new ReservationPassenger();
    $reservationPassengerRepository = $this->createMock(ReservationPassengerRepository::class);
    $reservationPassengerRepository
        ->expects($this->once())
        ->method('find')
        ->with(1)
        ->willReturn($reservationPassenger);

    $entityManager
        ->method('getRepository')
        ->willReturnMap([
            [Reservation::class, $reservationRepository],
            [ReservationPassenger::class, $reservationPassengerRepository],
        ]);

    $entityManager->expects($this->once())->method('remove');

    $entityManager->expects($this->once())->method('flush');
    
    static::getContainer()->set(
        DeleteReservationPassengerController::class,
        new DeleteReservationPassengerController(
            $entityManager,
            $validator,
            $objectMapper
        )
    );

    $client->jsonRequest('DELETE', '/api/reservations/1/passengers/1');

    expect($client->getResponse()->getStatusCode())
        ->not->toBe(404);

    expect($client->getResponse()->getStatusCode())
        ->not->toBe(405);
});

it ('removes a passenger in an unknown reservation (404)', function() {
    $client = $this->createClient();

    $client->jsonRequest('DELETE', '/api/reservations/200/passengers/1');

    expect($client->getResponse()->getStatusCode())
        ->toBe(404);

    $response = json_decode(
        $client->getResponse()->getContent(),
        true
    );

    expect($response['message'])
        ->toBe('Reservation not found.');
});

it ('removes unknown passenger in a reservation (404)', function() {
    $client = $this->createClient();

    $client->jsonRequest('DELETE', '/api/reservations/1/passengers/100');

    expect($client->getResponse()->getStatusCode())
        ->toBe(404);

    $response = json_decode(
        $client->getResponse()->getContent(),
        true
    );

    expect($response['message'])
        ->toBe('Passenger not found.');
});

it ('removes passenger in a reservation successfully (200)', function() {
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

    $reservationPassenger = new ReservationPassenger();
    $reservationPassengerRepository = $this->createMock(ReservationPassengerRepository::class);
    $reservationPassengerRepository
        ->expects($this->once())
        ->method('find')
        ->with(1)
        ->willReturn($reservationPassenger);

    $entityManager
        ->method('getRepository')
        ->willReturnMap([
            [Reservation::class, $reservationRepository],
            [ReservationPassenger::class, $reservationPassengerRepository],
        ]);

    $entityManager->expects($this->once())->method('remove');

    $entityManager->expects($this->once())->method('flush');
    
    static::getContainer()->set(
        DeleteReservationPassengerController::class,
        new DeleteReservationPassengerController(
            $entityManager,
            $validator,
            $objectMapper
        )
    );

    $client->jsonRequest('DELETE', '/api/reservations/1/passengers/1');

    expect($client->getResponse()->getStatusCode())
        ->toBe(204);
});