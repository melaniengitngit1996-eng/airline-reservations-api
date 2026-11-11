<?php

use App\Controller\ReservationPassenger\UpdateReservationPassengerController;
use App\Entity\Passenger;
use App\Entity\Reservation;
use App\Entity\ReservationPassenger;
use App\Entity\Seat;
use App\Repository\ReservationPassengerRepository;
use App\Repository\ReservationRepository;
use App\Repository\SeatRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\ObjectMapper\ObjectMapperInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

it('has update passenger API endpoint', function () {
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

    $passenger = new Passenger();
    $passenger->setFirstName('Juan');
    $passenger->setLastName('Dela Cruz');
    $passenger->setEmail('juan@example.com');

    $seat = new Seat();
    $seatRepository = $this->createMock(SeatRepository::class);
    $seatRepository
        ->expects($this->once())
        ->method('find')
        ->with(2)
        ->willReturn($seat);

    $reservationPassenger = new ReservationPassenger();
    $reservationPassenger->setPassenger($passenger);
    $reservationPassenger->setSeat($seat);

    $reservationPassengerRepository = $this->createMock(ReservationPassengerRepository::class);
    $reservationPassengerRepository
        ->expects($this->once())
        ->method('find')
        ->with(1)
        ->willReturn($reservationPassenger);
    $reservationPassengerRepository
        ->expects($this->once())
        ->method('isTaken')
        ->with($seat)
        ->willReturn(false);

    $entityManager
        ->method('getRepository')
        ->willReturnMap([
            [Reservation::class, $reservationRepository],
            [ReservationPassenger::class, $reservationPassengerRepository],
            [Seat::class, $seatRepository]
        ]);

    $entityManager->expects($this->once())->method('flush');
    
    static::getContainer()->set(
        UpdateReservationPassengerController::class,
        new UpdateReservationPassengerController(
            $entityManager,
            $validator,
            $objectMapper
        )
    );
    
    $client->jsonRequest('PATCH', '/api/reservations/1/passengers/1', [
        'seat_id' => 2
    ]);

    expect($client->getResponse()->getStatusCode())
        ->not->toBe(404);

    expect($client->getResponse()->getStatusCode())
        ->not->toBe(405);
});

it('updates seat of a passenger in an unknown reservation (404)', function() {
    $client = $this->createClient();

    $client->jsonRequest('PATCH', '/api/reservations/100/passengers/1', [
        'seat_id' => 2
    ]);

    expect($client->getResponse()->getStatusCode())
        ->toBe(404);

    $response = json_decode(
        $client->getResponse()->getContent(),
        true
    );

    expect($response['message'])
        ->toBe('Reservation not found.');
});

it('updates seat of an unknown passenger (404)', function() {
    $client = $this->createClient();

    $client->jsonRequest('PATCH', '/api/reservations/1/passengers/100', [
        'seat_id' => 2
    ]);

    expect($client->getResponse()->getStatusCode())
        ->toBe(404);

    $response = json_decode(
        $client->getResponse()->getContent(),
        true
    );

    expect($response['message'])
        ->toBe('Passenger not found.');
});

it('updates seat of a passenger with an already taken seat (409)', function () {
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

    $passenger = new Passenger();
    $passenger->setFirstName('Juan');
    $passenger->setLastName('Dela Cruz');
    $passenger->setEmail('juan@example.com');

    $seat = new Seat();
    $seatRepository = $this->createMock(SeatRepository::class);
    $seatRepository
        ->expects($this->once())
        ->method('find')
        ->with(2)
        ->willReturn($seat);

    $reservationPassenger = new ReservationPassenger();
    $reservationPassenger->setPassenger($passenger);
    $reservationPassenger->setSeat($seat);

    $reservationPassengerRepository = $this->createMock(ReservationPassengerRepository::class);
    $reservationPassengerRepository
        ->expects($this->once())
        ->method('find')
        ->with(1)
        ->willReturn($reservationPassenger);
    $reservationPassengerRepository
        ->expects($this->once())
        ->method('isTaken')
        ->with($seat)
        ->willReturn(true);

    $entityManager
        ->method('getRepository')
        ->willReturnMap([
            [Reservation::class, $reservationRepository],
            [ReservationPassenger::class, $reservationPassengerRepository],
            [Seat::class, $seatRepository]
        ]);
    
    static::getContainer()->set(
        UpdateReservationPassengerController::class,
        new UpdateReservationPassengerController(
            $entityManager,
            $validator,
            $objectMapper
        )
    );

    $client->jsonRequest('PATCH', '/api/reservations/1/passengers/1', [
        'seat_id' => 2
    ]);

    expect($client->getResponse()->getStatusCode())
        ->toBe(409);

    $response = json_decode(
        $client->getResponse()->getContent(),
        true
    );

    expect($response['message'])
        ->toBe('Seat already taken.');
});

it('updates seat of a passenger successfully', function () {
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

    $passenger = new Passenger();
    $passenger->setFirstName('Juan');
    $passenger->setLastName('Dela Cruz');
    $passenger->setEmail('juan@example.com');

    $seat = new Seat();
    $seatRepository = $this->createMock(SeatRepository::class);
    $seatRepository
        ->expects($this->once())
        ->method('find')
        ->with(2)
        ->willReturn($seat);

    $reservationPassenger = new ReservationPassenger();
    $reservationPassenger->setPassenger($passenger);
    $reservationPassenger->setSeat($seat);

    $reservationPassengerRepository = $this->createMock(ReservationPassengerRepository::class);
    $reservationPassengerRepository
        ->expects($this->once())
        ->method('find')
        ->with(1)
        ->willReturn($reservationPassenger);
    $reservationPassengerRepository
        ->expects($this->once())
        ->method('isTaken')
        ->with($seat)
        ->willReturn(false);

    $entityManager
        ->method('getRepository')
        ->willReturnMap([
            [Reservation::class, $reservationRepository],
            [ReservationPassenger::class, $reservationPassengerRepository],
            [Seat::class, $seatRepository]
        ]);

    $entityManager->expects($this->once())->method('flush');
    
    static::getContainer()->set(
        UpdateReservationPassengerController::class,
        new UpdateReservationPassengerController(
            $entityManager,
            $validator,
            $objectMapper
        )
    );

    $client->jsonRequest('PATCH', '/api/reservations/1/passengers/1', [
        'seat_id' => 2
    ]);

    expect($client->getResponse()->getStatusCode())
        ->toBe(200);

    $response = json_decode(
        $client->getResponse()->getContent(),
        true
    );

    expect($response)->toHaveKey('seat');
});