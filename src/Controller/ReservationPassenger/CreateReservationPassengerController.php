<?php

namespace App\Controller\ReservationPassenger;

use App\Controller\AbstractController;
use App\Dto\ReservationPassenger\CreateReservationPassengerDto;
use App\Entity\Reservation;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Controller for adding passengers to an existing reservation.
 */
class CreateReservationPassengerController extends AbstractController
{
    /**
     * Adds one or more passengers to an existing reservation.
     *
     * @param int     $reservationId The ID of the reservation.
     * @param Request $request       The HTTP request containing passenger data.
     *
     * @return JsonResponse The list of all the passengers associated in the reservation or an error response.
     */
    #[Route('/api/reservations/{reservationId}/passengers', methods: ['POST'])]
    public function __invoke($reservationId, Request $request)
    {
        $data = json_decode($request->getContent(), true);

        $passengers = $data['passengers'] ?? [];

        if (empty($passengers)) {
            return $this->errorResponse(
                'Passenger not found.',
                404
            );
        }

        foreach ($passengers as $passenger) {
            $passengerDto = new CreateReservationPassengerDto();

            $passengerDto->passengerId = $passenger['passenger_id'] ?? 0;
            $passengerDto->seatId = $passenger['seat_id'] ?? 0;

            $errors = $this->validator->validate($passengerDto);

            if (count($errors) > 0) {
                return $this->errorResponse(
                    $errors[0]->getMessage(),
                    404,
                    (string) $errors
                );
            }
        }

        $reservationRepository = $this->entityManager->getRepository(Reservation::class);
        $reservation = $reservationRepository->find($reservationId);

        if ($reservation === null) {
            return $this->errorResponse(
                'Reservation not found.',
                404
            );
        }

        $response = $this->addReservationPassenger($reservation, $passengers);

        if ($response !== null) {
            return $response;
        }

        $this->entityManager->persist($reservation);
        $this->entityManager->flush();

        return $this->successResponse([
            'passengers' => array_map(
                function ($reservationPassenger) {
                    return [
                        'id' => $reservationPassenger->getPassenger()->getId(),
                        'first_name' => $reservationPassenger->getPassenger()->getFirstName(),
                        'last_name' => $reservationPassenger->getPassenger()->getLastName(),
                        'email' => $reservationPassenger->getPassenger()->getEmail(),
                        'seat' => [
                            'id' => $reservationPassenger->getSeat()->getId(),
                            'code' => $reservationPassenger->getSeat()->getCode(),
                        ],
                    ];
                },
                $reservation->getReservationPassengers()->toArray()
            ),
        ], 201);
    }
}