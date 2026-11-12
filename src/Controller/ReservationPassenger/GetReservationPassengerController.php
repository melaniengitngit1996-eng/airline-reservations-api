<?php

namespace App\Controller\ReservationPassenger;

use App\Controller\AbstractController;
use App\Entity\Reservation;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Controller for retrieving passengers assigned to a reservation.
 */
class GetReservationPassengerController extends AbstractController
{
    /**
     * Retrieves all passenger assignments for a reservation.
     *
     * @param int $reservationId The ID of the reservation.
     *
     * @return JsonResponse The list of all the passengers associated in the reservation or an error response.
     */
    #[Route('/api/reservations/{reservationId}/passengers', methods: ['GET'])]
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
        ], 200);
    }
}