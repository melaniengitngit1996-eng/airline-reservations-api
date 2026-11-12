<?php

use App\Controller\Reservation\CreateReservationController;
use App\Entity\Airport;
use App\Entity\Flight;
use App\Entity\Passenger;
use App\Entity\ReservationPassenger;
use App\Entity\Seat;
use App\Repository\FlightRepository;
use App\Repository\PassengerRepository;
use App\Repository\ReservationPassengerRepository;
use App\Repository\SeatRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\ObjectMapper\ObjectMapperInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

it('has create reservation API endpoint', function () {
    $client = $this->createClient();

    $client->jsonRequest('POST', '/api/reservations', [
        "flight_id" => 2,
        'passengers' => [
            [
                'passenger_id' => 1,
                'seat_id' => 5,
            ]
        ],
    ]);

    expect($client->getResponse()->getStatusCode())
        ->not->toBe(404);
});

it('should not return 405', function () {
    $client = $this->createClient();

    $client->jsonRequest('POST', '/api/reservations', []);

    expect($client->getResponse()->getStatusCode())
        ->not->toBe(405);
});

it('creates a reservation without passengers (404)', function () {
    $client = $this->createClient();

    $client->jsonRequest('POST', '/api/reservations', [
        "flight_id" => 2,
        "passengers" => []
    ]);

    expect($client->getResponse()->getStatusCode())
        ->toBe(404);

    $response = json_decode(
        $client->getResponse()->getContent(),
        true
    );

    expect($response['message'])
        ->toBe('Passengers not found.');
});

it('creates a reservation with a missing passenger field (400)', function () {
    $client = $this->createClient();

    $client->jsonRequest('POST', '/api/reservations', [
        "flight_id" => 2,
    ]);

    expect($client->getResponse()->getStatusCode())
        ->toBe(404);

    $response = json_decode(
        $client->getResponse()->getContent(),
        true
    );

    expect($response['message'])
        ->toBe('Passengers not found.');
});

it('creates a reservation with unknown flight (404)', function () {
    $client = $this->createClient();

    $client->jsonRequest('POST', '/api/reservations', [
        "flight_id" => 100,
        'passengers' => [
            [
                'passenger_id' => 1,
                'seat_id' => 5,
            ],
            [
                'passenger_id' => 2,
                'seat_id' => 6,
            ],
        ],
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

it('creates a reservation with unknown passenger (404)', function () {
    $client = $this->createClient();

    $client->jsonRequest('POST', '/api/reservations', [
        "flight_id" => 1,
        'passengers' => [
            [
                'passenger_id' => 100,
                'seat_id' => 5,
            ],
        ],
    ]);

    expect($client->getResponse()->getStatusCode())
        ->toBe(404);

    $response = json_decode(
        $client->getResponse()->getContent(),
        true
    );

    expect($response['message'])
        ->toBe('Unknown passenger.');
});

it('creates a reservation with unknown seat (404)', function () {
    $client = $this->createClient();

    $client->jsonRequest('POST', '/api/reservations', [
        "flight_id" => 1,
        'passengers' => [
            [
                'passenger_id' => 1,
                'seat_id' => 500,
            ],
        ],
    ]);

    expect($client->getResponse()->getStatusCode())
        ->toBe(404);

    $response = json_decode(
        $client->getResponse()->getContent(),
        true
    );

    expect($response['message'])
        ->toBe('Unknown seat.');
});

it('creates a reservation with an already taken seat (409)', function () {
    $client = $this->createClient();

    $client->jsonRequest('POST', '/api/reservations', [
        "flight_id" => 1,
        'passengers' => [
            [
                'passenger_id' => 1,
                'seat_id' => 2,
            ],
        ],
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

it('creates a reservation with passengers successfully (201)', function () {
    $client = $this->createClient();

    $entityManager = $this->createMock(EntityManagerInterface::class);
    $validator = $this->createMock(ValidatorInterface::class);
    $objectMapper = $this->createMock(ObjectMapperInterface::class);

    $departureAirport = new Airport();
    $arrivalAirport = new Airport();

    $flight = new Flight();
    $flight->setFlightNumber('5J 744');
    $flight->setOriginAirport($departureAirport);
    $flight->setDestinationAirport($arrivalAirport);
    $flight->setDepartureTime(
        new DateTimeImmutable('2026-11-10T06:00:00+08:00')
    );
    $flight->setArrivalTime(
        new DateTimeImmutable('2026-11-10T07:30:00+08:00')
    );

    $flightRepository = $this->createMock(FlightRepository::class);
    $flightRepository
        ->expects($this->once())
        ->method('find')
        ->with(2)
        ->willReturn($flight);

    // Passenger
    $passenger = new Passenger();
    $passenger->setFirstName('Juan');
    $passenger->setLastName('Dela Cruz');
    $passenger->setEmail('juan@example.com');

    $passengerRepository = $this->createMock(PassengerRepository::class);
    $passengerRepository
        ->expects($this->once())
        ->method('find')
        ->with(1)
        ->willReturn($passenger);

    // Seat
    $seat = new Seat();
    $seat->setCode('A1');
    $seat->setFlight($flight);

    $seatRepository = $this->createMock(SeatRepository::class);
    $seatRepository
        ->expects($this->once())
        ->method('find')
        ->with(5)
        ->willReturn($seat);

    $reservationPassengerRepository = $this->createMock(ReservationPassengerRepository::class); 
    $reservationPassengerRepository
        ->expects($this->once())
        ->method('isTaken')
        ->with($seat)
        ->willReturn(false);

    $entityManager
        ->method('getRepository')
        ->willReturnMap([
            [Flight::class, $flightRepository],
            [Passenger::class, $passengerRepository],
            [Seat::class, $seatRepository],
            [ReservationPassenger::class, $reservationPassengerRepository],
        ]);

    // Persist reservation
    $entityManager
        ->expects($this->exactly(2))
        ->method('persist');

    $entityManager
        ->expects($this->once())
        ->method('flush');

    static::getContainer()->set(
        CreateReservationController::class,
        new CreateReservationController(
            $entityManager,
            $validator,
            $objectMapper
        )
    );

    $client->jsonRequest('POST', '/api/reservations', [
        'flight_id' => 2,
        'passengers' => [
            [
                'passenger_id' => 1,
                'seat_id' => 5,
            ]
        ],
    ]);

    expect($client->getResponse()->getStatusCode())
        ->toBe(201);
});