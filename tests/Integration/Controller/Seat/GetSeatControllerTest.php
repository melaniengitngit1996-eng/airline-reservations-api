<?php

use App\Controller\Seat\GetSeatsController;
use App\Entity\Flight;
use App\Entity\ReservationPassenger;
use App\Entity\Seat;
use App\Repository\FlightRepository;
use App\Repository\ReservationPassengerRepository;
use App\Repository\SeatRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\ObjectMapper\ObjectMapperInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

it('gets all seats of an unknown flight. (404)', function () {
    $client = $this->createClient();

    $entityManager = $this->createMock(EntityManagerInterface::class);
    $validator = $this->createMock(ValidatorInterface::class);
    $objectMapper = $this->createMock(ObjectMapperInterface::class);

    static::getContainer()->set(
        GetSeatsController::class,
        new GetSeatsController(
            $entityManager,
            $validator,
            $objectMapper
        )
    );

    $flightRepository = $this->createMock(FlightRepository::class);

    $flightRepository
        ->method('find')
        ->with(999)
        ->willReturn(null);

    $entityManager
        ->method('getRepository')
        ->with(Flight::class)
        ->willReturn($flightRepository);

    $client->request('GET', '/api/flights/999/seats');

    expect($client->getResponse()->getStatusCode())->toBe(404)
        ->and(json_decode($client->getResponse()->getContent(), true))->toBe([
            'message' => 'Flight not found.',
        ]);
});

it('gets all seats of a flight. (200)', function () {
    $client = $this->createClient();

    $entityManager = $this->createMock(EntityManagerInterface::class);
    $validator = $this->createMock(ValidatorInterface::class);
    $objectMapper = $this->createMock(ObjectMapperInterface::class);

    static::getContainer()->set(
        GetSeatsController::class,
        new GetSeatsController(
            $entityManager,
            $validator,
            $objectMapper
        )
    );

    $flight = $this->createMock(Flight::class);
    $flight->method('getId')->willReturn(1);

    $seat1 = $this->createMock(Seat::class);
    $seat1->method('getId')->willReturn(1);
    $seat1->method('getFlight')->willReturn($flight);
    $seat1->method('getCode')->willReturn('1A');

    $seat2 = $this->createMock(Seat::class);
    $seat2->method('getId')->willReturn(2);
    $seat2->method('getFlight')->willReturn($flight);
    $seat2->method('getCode')->willReturn('1B');

    $flightRepository = $this->createMock(FlightRepository::class);
    $flightRepository
        ->method('find')
        ->with(1)
        ->willReturn($flight);

    $seatRepository = $this->createMock(SeatRepository::class);
    $seatRepository
        ->method('findBy')
        ->with(['flight' => $flight])
        ->willReturn([$seat1, $seat2]);

    $reservationPassengerRepository = $this->createMock(ReservationPassengerRepository::class);
    $reservationPassengerRepository
        ->method('isTaken')
        ->willReturn(false);

    $entityManager
        ->method('getRepository')
        ->willReturnMap([
            [Flight::class, $flightRepository],
            [Seat::class, $seatRepository],
            [ReservationPassenger::class, $reservationPassengerRepository],
        ]);

    $client->request('GET', '/api/flights/1/seats');

    expect($client->getResponse()->getStatusCode())->toBe(200)
        ->and(json_decode($client->getResponse()->getContent(), true))->toBe([
            [
                'id' => 1,
                'flight_id' => 1,
                'code' => '1A',
                'taken' => false,
            ],
            [
                'id' => 2,
                'flight_id' => 1,
                'code' => '1B',
                'taken' => false,
            ],
        ]);
});