<?php

namespace App\Controller\ReservationPassenger;

use App\Controller\AbstractController;
use App\Entity\Reservation;
use App\Entity\ReservationPassenger;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * 
 * Controller for removing a passenger assignment from an existing reservation.
 */
class DeleteReservationPassengerController extends AbstractController
{
    /**
     * Removes a passenger assignment from a reservation.
     *
     * @param int $reservationId The ID of the reservation.
     * @param int $passengerId   The ID of the passenger assignment.
     *
     * @return JsonResponse The successful response or an error response.
     */
    #[Route('/api/reservations/{reservationId}/passengers/{passengerId}', methods: ['DELETE'])]
    public function __invoke(int $reservationId, int $passengerId)
    {
        $reservationRepository = $this->entityManager->getRepository(Reservation::class);
        $reservation = $reservationRepository->find($reservationId);

        if ($reservation === null) {
            return $this->errorResponse(
                'Reservation not found.',
                404
            );
        }

        $reservationPassengerRepository = $this->entityManager->getRepository(ReservationPassenger::class);
        $reservationPassenger = $reservationPassengerRepository->find($passengerId);

        if ($reservationPassenger === null) {
            return $this->errorResponse(
                'Passenger not found.',
                404
            );
        }

        $this->entityManager->remove($reservationPassenger);

        $this->entityManager->flush();

        return $this->successResponse([
            'message' => 'Passenger assignment removed successfully.'
        ], 200);
    }
}