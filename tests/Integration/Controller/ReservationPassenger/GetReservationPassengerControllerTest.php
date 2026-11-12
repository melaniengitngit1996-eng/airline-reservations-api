<?php

use App\Controller\ReservationPassenger\GetReservationPassengerController;
use App\Entity\Passenger;
use App\Entity\Reservation;
use App\Entity\ReservationPassenger;
use App\Entity\Seat;
use App\Repository\ReservationPassengerRepository;
use App\Repository\ReservationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\ObjectMapper\ObjectMapperInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

it('has get reservation passengers API endpoint', function () {
    $client = $this->createClient();

    $client->jsonRequest('GET', '/api/reservations/1/passengers');

    expect($client->getResponse()->getStatusCode())
        ->not->toBe(404);

    expect($client->getResponse()->getStatusCode())
        ->not->toBe(405);
});

it ('gets passenger in an unknown reservation (404)', function () {
    $client = $this->createClient();

    $client->jsonRequest('GET', '/api/reservations/100/passengers');

    expect($client->getResponse()->getStatusCode())
        ->toBe(404);

    $response = json_decode(
        $client->getResponse()->getContent(),
        true
    );

    expect($response['message'])
        ->toBe('Reservation not found.');
});

it ('gets passenger in a reservation successfully (200)', function () {
    $client = $this->createClient();

    $entityManager = $this->createMock(EntityManagerInterface::class);
    $validator = $this->createMock(ValidatorInterface::class);
    $objectMapper = $this->createMock(ObjectMapperInterface::class);

    $passenger = new Passenger();
    $passenger->setFirstName('Juan');
    $passenger->setLastName('Dela Cruz');
    $passenger->setEmail('juan@example.com');

    $seat = new Seat();
    $seat->setCode('A1');

    $reservationPassenger = new ReservationPassenger();
    $reservationPassenger->setPassenger($passenger);
    $reservationPassenger->setSeat($seat);

    $reservation = new Reservation();
    $reservation->addReservationPassenger($reservationPassenger);
    
    $reservationRepository = $this->createMock(ReservationRepository::class);
    $entityManager->method('getRepository')->willReturn($reservationRepository);

    $reservationRepository
        ->expects($this->once())
        ->method('find')
        ->with(1)
        ->willReturn($reservation);

    static::getContainer()->set(
        GetReservationPassengerController::class,
        new GetReservationPassengerController(
            $entityManager,
            $validator,
            $objectMapper
        )
    );

    $client->jsonRequest('GET', '/api/reservations/1/passengers');

    expect($client->getResponse()->getStatusCode())
        ->toBe(200);

    $response = json_decode(
        $client->getResponse()->getContent(),
        true
    );

    expect($response['passengers'][0]['first_name'])->toBe('Juan');
});