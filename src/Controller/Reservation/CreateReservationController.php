<?php

namespace App\Controller\Reservation;

use App\Controller\AbstractController;
use App\Dto\Reservation\CreateReservationDto;
use App\Dto\ReservationPassenger\CreateReservationPassengerDto;
use App\Entity\Flight;
use App\Entity\Reservation;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class CreateReservationController extends AbstractController
{
    #[Route('/api/reservations', methods: ['POST'])]
    public function __invoke(Request $request)
    {
        $data = json_decode($request->getContent(), true);

        $dto = new CreateReservationDto();

        $dto->flightId = $data['flight_id'] ?? 0;
        $dto->passengers = [];

        $passengers = $data['passengers'] ?? [];

        foreach ($passengers as $passenger) {
            $passengerDto = new CreateReservationPassengerDto();

            $passengerDto->passengerId = $passenger['passenger_id'] ?? 0;
            $passengerDto->seatId = $passenger['seat_id'] ?? 0;

            $dto->passengers[] = $passengerDto;
        }

        $errors = $this->validator->validate($dto);

        if (count($errors) > 0) {
            $message = $errors[0]->getMessage();
            $errorCode = 400;

            if (str_contains($message, 'not found')) {
                $errorCode = 404;
            }

            return $this->errorResponse(
                $message,
                $errorCode,
                (string) $errors
            );
        }

        $flightRepository = $this->entityManager->getRepository(Flight::class);
        
        $flight = $flightRepository->find($dto->flightId);

        if ($flight === null) {
            return $this->errorResponse(
                'Flight not found.',
                404
            );
        }

        $reservation = new Reservation();
        $reservation->setFlight($flight);

        $response = $this->addReservationPassenger($reservation, $passengers);

        if ($response !== null) {
            return $response;
        }

        $this->entityManager->persist($reservation);
        $this->entityManager->flush();

        return $this->successResponse([
            'id' => $reservation->getId(),
            'flight' => [
                'flight_id' => $flight->getId(),
                'flight_number' => $flight->getFlightNumber(),
                'departure_airport_id' => $flight->getOriginAirport()->getId(),
                'arrival_airport_id' => $flight->getDestinationAirport()->getId(),
                'departure_time' => $flight->getDepartureTime()->format('Y-m-d H:i:s.u'),
                'arrival_time' => $flight->getArrivalTime()->format('Y-m-d H:i:s.u'),
                'capacity' => $flight->getCapacity(),
            ],
            'passengers' => array_map(
                function ($reservationPassenger) {
                    return [
                        'passenger_id' => $reservationPassenger->getPassenger()->getId(),
                        'first_name' => $reservationPassenger->getPassenger()->getFirstName(),
                        'last_name' => $reservationPassenger->getPassenger()->getLastName(),
                        'email' => $reservationPassenger->getPassenger()->getEmail(),
                        'seat' => [
                            'seat_id' => $reservationPassenger->getSeat()->getId(),
                            'code' => $reservationPassenger->getSeat()->getCode(),
                        ],
                    ];
                },
                $reservation->getReservationPassengers()->toArray()
            ),
        ], 201);
    }
}