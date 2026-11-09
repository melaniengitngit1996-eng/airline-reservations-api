<?php

namespace App\Controller;

use App\Service\ReservationServiceInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/reservations')]
class ReservationController {
    public function __construct(private ReservationServiceInterface $reservationService) {}

    #[Route('', methods: ['POST'])]
    public function create(Request $request) {
        $data = json_decode($request->getContent(), true);

        if (empty($data['flight_id'])) {
            return new JsonResponse([
                'message' => 'Flight not found.'
            ], 404);
        }
        
        try {
            $reservation = $this->reservationService->create($data);
        } catch (\DomainException $e) {
            return new JsonResponse([
                'message' => $e->getMessage()
            ], 404);
        }

        return new JsonResponse([
            'reservation_id' => $reservation->getId(),
            'flight' => [
                'flight_id' => $reservation->getFlight()->getId(),
                'flight_number' => $reservation->getFlight()->getFlightNumber(),
                'departure_airport_id' => $reservation->getFlight()->getOriginAirport()->getId(),
                'arrival_airport_id' => $reservation->getFlight()->getDestinationAirport()->getId(),
                'departure_time' => $reservation->getFlight()->getDepartureTime()->format(DATE_ATOM),
                'arrival_time' => $reservation->getFlight()->getArrivalTime()->format(DATE_ATOM),
            ],
        ], 201);
    }

    #[Route('', methods: ['GET'])]
    public function index(Request $request): JsonResponse
    {
        $flightId = $request->query->get('flight_id');

        $reservations = $this->reservationService->findAll(
            $flightId !== null ? (int) $flightId : null
        );

        return new JsonResponse(array_map(
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
        ));
    }

    #[Route('/{id}', methods: ['GET'])]
    public function show(int $id): JsonResponse
    {
        $reservation = $this->reservationService->find($id);

        if (!$reservation) {
            return new JsonResponse(['message' => 'Reservation not found'], 404);
        }

        return new JsonResponse([
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
        ]);
    }

    #[Route('/{id}', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        try {
            $this->reservationService->delete($id);
        } catch (\DomainException $e) {
            return new JsonResponse([
                'message' => $e->getMessage()
            ], 404);
        }

        return new JsonResponse(null, 204);
    }
}