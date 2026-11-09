<?php

use App\Entity\Flight;
use App\Entity\Seat;
use App\Repository\FlightRepository;
use App\Repository\ReservationPassengerRepository;
use App\Repository\SeatRepository;
use App\Service\SeatService;
use Doctrine\ORM\EntityManagerInterface;

it('creates a new seat in a flight', function () {
    $flight = new Flight();

    $flightRepository = $this->createMock(FlightRepository::class);
    $seatRepository = $this->createMock(SeatRepository::class);
    $reservationPassengerRepository = $this->createMock(ReservationPassengerRepository::class);
    $entityManager = $this->createMock(EntityManagerInterface::class);

    $flightRepository->expects($this->once())
        ->method('find')
        ->with(1)
        ->willReturn($flight);

    $entityManager->expects($this->once())
        ->method('persist');

    $entityManager->expects($this->once())
        ->method('flush');

    $service = new SeatService(
        $flightRepository, 
        $seatRepository, 
        $reservationPassengerRepository,
        $entityManager
    );

    $seat = $service->create(1, [
        'code' => 'ZZ'
    ]);

    expect($seat)->toBeInstanceOf(Seat::class);
});

it('gets all seats in a flight', function () {
    $flightRepository = $this->createMock(FlightRepository::class);
    $reservationPassengerRepository = $this->createMock(ReservationPassengerRepository::class);
    $flight = new Flight();
    $flightRepository->expects($this->once())
        ->method('find')
        ->with(1)
        ->willReturn($flight);
        
    $seat = new Seat();
    $seatRepository = $this->createMock(SeatRepository::class);
    $seatRepository->expects($this->once())
        ->method('findByFlight')
        ->willReturn([$seat]);

    $entityManager = $this->createMock(EntityManagerInterface::class);

    $service = new SeatService(
        $flightRepository, 
        $seatRepository, 
        $reservationPassengerRepository,
        $entityManager
    );  
      
    $seats = $service->findByFlight(1);

    expect($seats)->toHaveCount(1);
    expect($seats[0])->toBeInstanceOf(Seat::class);
});

it('gets a seat in a flight', function () {
    $flight = new Flight();
    $seat = new Seat();

    $seatRepository = $this->createMock(SeatRepository::class);
    $flightRepository = $this->createMock(FlightRepository::class);
    $entityManager = $this->createMock(EntityManagerInterface::class);
    $reservationPassengerRepository = $this->createMock(ReservationPassengerRepository::class);

    $flightRepository->expects($this->once())
        ->method('find')
        ->with(1)
        ->willReturn($flight);

    $seatRepository->expects($this->once())
        ->method('findOneBy')
        ->with([
            'id' => 1,
            'flight' => $flight,
        ])
        ->willReturn($seat);

    $service = new SeatService(
        $flightRepository,
        $seatRepository,
        $reservationPassengerRepository,
        $entityManager
    );

    $seat = $service->find(1, 1);

    expect($seat)->toBeInstanceOf(Seat::class);
});

it('updates a seat in a flight', function () {
    $flight = new Flight();
    $flightRepository = $this->createMock(FlightRepository::class);
    $flightRepository->expects($this->once())
        ->method('find')
        ->with(1)
        ->willReturn($flight);

    $seat = new Seat();
    $seatRepository = $this->createMock(SeatRepository::class);
    $seatRepository->expects($this->once())
        ->method('find')
        ->with(1)
        ->willReturn($seat);

    $reservationPassengerRepository = $this->createMock(ReservationPassengerRepository::class);

    $entityManager = $this->createMock(EntityManagerInterface::class);

    $entityManager->expects($this->once())
        ->method('persist');

    $entityManager->expects($this->once())
        ->method('flush');

    $service = new SeatService(
        $flightRepository,
        $seatRepository,
        $reservationPassengerRepository,
        $entityManager
    );

    $result = $service->update(1, 1, [
        'code' => 'A6',
    ]);

    expect($result->getCode())->toBe('A6');
});

it('deletes a seat in a flight', function () {
    $flightRepository = $this->createMock(FlightRepository::class);

    $seat = new Seat();
    $seatRepository = $this->createStub(SeatRepository::class);
    $seatRepository->method('find')
        ->willReturn($seat);

    $flight = new Flight();
    $flightRepository = $this->createMock(FlightRepository::class);
    $flightRepository->expects($this->once())
        ->method('find')
        ->with(1)
        ->willReturn($flight);

    $entityManager = $this->createMock(EntityManagerInterface::class);

    $reservationPassengerRepository = $this->createMock(ReservationPassengerRepository::class);

    $entityManager->expects($this->once())
        ->method('remove');

    $entityManager->expects($this->once())
        ->method('flush');

    $service = new SeatService(
        $flightRepository,
        $seatRepository,
        $reservationPassengerRepository,
        $entityManager
    );

    $result = $service->delete(1, 1);

    expect($result)->toBeTrue();
});

it('checks if a seat is taken', function () {
    $seat = new Seat();

    $reservationPassengerRepository = $this->createMock(
        ReservationPassengerRepository::class
    );

    $reservationPassengerRepository->expects($this->once())
        ->method('isTaken')
        ->with($seat)
        ->willReturn(true);

    $seatRepository = $this->createStub(SeatRepository::class);
    $flightRepository = $this->createMock(FlightRepository::class);
    $entityManager = $this->createMock(EntityManagerInterface::class);

    $service = new SeatService(
        $flightRepository,
        $seatRepository,
        $reservationPassengerRepository,
        $entityManager
    );

    $result = $service->isTaken($seat);

    expect($result)->toBeTrue();
});