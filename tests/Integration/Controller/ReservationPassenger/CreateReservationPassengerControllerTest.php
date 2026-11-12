<?php

use App\Entity\Flight;
use App\Entity\Passenger;
use App\Entity\Reservation;
use App\Entity\ReservationPassenger;
use App\Entity\Seat;
use App\Repository\ReservationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\ObjectMapper\ObjectMapperInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Controller\ReservationPassenger\CreateReservationPassengerController;
use App\Repository\PassengerRepository;
use App\Repository\ReservationPassengerRepository;
use App\Repository\SeatRepository;

it('has create passenger API endpoint', function () {
    $client = $this->createClient();

    $client->jsonRequest('POST', '/api/reservations/1/passengers', [
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

it('adds passenger with missing passenger id (404)', function () {
    $client = $this->createClient();

    $client->jsonRequest('POST', '/api/reservations/1/passengers', [
        'passengers' => [
            [
                'seat_id' => 5,
            ]
        ],
    ]);

    expect($client->getResponse()->getStatusCode())
        ->toBe(404);

    $response = json_decode(
        $client->getResponse()->getContent(),
        true
    );

    expect($response['message'])
        ->toBe('Required fields are missing.');
});

it('adds passenger with missing seat id (404)', function () {
    $client = $this->createClient();

    $client->jsonRequest('POST', '/api/reservations/1/passengers', [
        'passengers' => [
            [
                'passenger_id' => 1,
            ]
        ],
    ]);

    expect($client->getResponse()->getStatusCode())
        ->toBe(404);

    $response = json_decode(
        $client->getResponse()->getContent(),
        true
    );

    expect($response['message'])
        ->toBe('Required fields are missing.');
});

it('adds passenger with unknown reservation (404)', function () {
    $client = $this->createClient();

    $client->jsonRequest('POST', '/api/reservations/100/passengers', [
        'passengers' => [
            [
                'passenger_id' => 1,
                'seat_id' => 5,
            ]
        ],
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

it('adds passenger with unknown passenger (404)', function () {
    $client = $this->createClient();

    $client->jsonRequest('POST', '/api/reservations/1/passengers', [
        'passengers' => [
            [
                'passenger_id' => 100,
                'seat_id' => 5,
            ]
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

it('adds passenger with unknown seat (404)', function () {
    $client = $this->createClient();

    $client->jsonRequest('POST', '/api/reservations/1/passengers', [
        'passengers' => [
            [
                'passenger_id' => 1,
                'seat_id' => 500,
            ]
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

it('adds passenger successfully (201)', function () {
    $client = $this->createClient();

    $entityManager = $this->createMock(EntityManagerInterface::class);
    $validator = $this->createMock(ValidatorInterface::class);
    $objectMapper = $this->createMock(ObjectMapperInterface::class);

    $flight = new Flight();
    $reservation = new Reservation();
    $reservation->setFlight($flight);

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

    $passengerRepository = $this->createMock(PassengerRepository::class);
    $passengerRepository
        ->expects($this->once())
        ->method('find')
        ->with(1)
        ->willReturn($passenger);

    $seat = new Seat();
    $seat->setCode('A1');

    $seatRepository = $this->createMock(SeatRepository::class);
    $seatRepository
        ->expects($this->once())
        ->method('find')
        ->with(5)
        ->willReturn($seat);

    $reservationPassenger = new ReservationPassenger();
    $reservationPassenger->setPassenger($passenger);
    $reservationPassenger->setSeat($seat);
    $reservation->addReservationPassenger($reservationPassenger);

    $reservationPassengerRepository = $this->createMock(ReservationPassengerRepository::class); 
    $reservationPassengerRepository
        ->expects($this->once())
        ->method('isTaken')
        ->with($seat)
        ->willReturn(false);

    $entityManager
        ->method('getRepository')
        ->willReturnMap([
            [Reservation::class, $reservationRepository],
            [Passenger::class, $passengerRepository],
            [Seat::class, $seatRepository],
            [ReservationPassenger::class, $reservationPassengerRepository],
        ]);
    
    $entityManager->expects($this->exactly(2))->method('persist');
    $entityManager->expects($this->once())->method('flush');

    static::getContainer()->set(
        CreateReservationPassengerController::class,
        new CreateReservationPassengerController(
            $entityManager,
            $validator,
            $objectMapper
        )
    );

    $client->jsonRequest('POST', '/api/reservations/1/passengers', [
        'passengers' => [
            [
                'passenger_id' => 1,
                'seat_id' => 5,
            ]
        ],
    ]);

    expect($client->getResponse()->getStatusCode())
        ->toBe(201);

    $response = json_decode(
        $client->getResponse()->getContent(),
        true
    );

    expect($response)->toHaveKey('passengers');
});