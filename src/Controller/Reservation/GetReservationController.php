<?php

namespace App\Controller\Reservation;

use App\Controller\AbstractController;
use App\Entity\Reservation;
use Symfony\Component\Routing\Attribute\Route;

class GetReservationController extends AbstractController {
    #[Route('/api/reservations/{reservationId}', methods: ['GET'])]
    public function __invoke(int $reservationId)
    {
        $reservationRepository = $this->entityManager->getRepository(Reservation::class);

        $reservation = $reservationRepository->find($reservationId);

        if ($reservation === null) {
            return $this->errorResponse(
                'Reservation not found.',
                404
            );
        }

        return $this->successResponse([
            'id' => $reservation->getId(),
            'flight' => [
                'id' => $reservation->getFlight()->getId(),
                'flight_number' => $reservation->getFlight()->getFlightNumber(),
                'departure_airport_id' => $reservation->getFlight()->getOriginAirport()->getId(),
                'arrival_airport_id' => $reservation->getFlight()->getDestinationAirport()->getId(),
                'departure_time' => $reservation->getFlight()->getDepartureTime()->format('Y-m-d H:i:s.u'),
                'arrival_time' => $reservation->getFlight()->getArrivalTime()->format('Y-m-d H:i:s.u'),
                'capacity' => $reservation->getFlight()->getCapacity(),
            ],
            'passengers' => array_map(
                function ($reservationPassenger) {
                    return [
                        'id' => $reservationPassenger->getPassenger()->getId(),
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
        ], 200);
    }
}