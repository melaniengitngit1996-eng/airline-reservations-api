<?php

namespace App\Controller\Reservation;

use App\Controller\AbstractController;
use App\Entity\Reservation;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class GetAllReservationsController extends AbstractController {
    #[Route('/api/reservations', methods: ['GET'])]
    public function __invoke(Request $request)
    {
        $reservationRespository = $this->entityManager->getRepository(Reservation::class);

        $data = json_decode($request->getContent(), true);

        if (isset($data['flight_id']) === true)
            $reservations = $reservationRespository->findByFlightId($data['flight_id']);
        else
            $reservations = $reservationRespository->findAll();

        return $this->successResponse(array_map(
            fn ($reservation) => [
                'reservation_id' => $reservation->getId(),

                'flight' => [
                    'flight_id' => $reservation->getFlight()->getId(),
                    'flight_number' => $reservation->getFlight()->getFlightNumber(),
                    'departure_airport_id' => $reservation->getFlight()->getOriginAirport()->getId(),
                    'arrival_airport_id' => $reservation->getFlight()->getDestinationAirport()->getId(),
                    'departure_time' => $reservation->getFlight()->getDepartureTime()->format(DATE_ATOM),
                    'arrival_time' => $reservation->getFlight()->getArrivalTime()->format(DATE_ATOM),
                ],

                'passengers' => array_map(
                    fn ($reservationPassenger) => [
                        'passenger_id' => $reservationPassenger->getPassenger()->getId(),
                        'first_name' => $reservationPassenger->getPassenger()->getFirstName(),
                        'last_name' => $reservationPassenger->getPassenger()->getLastName(),
                        'email' => $reservationPassenger->getPassenger()->getEmail(),
                    ],
                    $reservation->getReservationPassengers()->toArray()
                ),
            ],
            $reservations
        ), 200);
    }
}