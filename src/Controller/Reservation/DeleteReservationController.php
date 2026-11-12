<?php

namespace App\Controller\Reservation;

use App\Controller\AbstractController;
use App\Entity\Reservation;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Controller for deleting reservations.
 */
class DeleteReservationController extends AbstractController
{
    /**
     * Deletes an existing reservation.
     *
     * @param int $reservationId The ID of the reservation to delete.
     *
     * @return JsonResponse The success code or an error response.
     */
    #[Route('/api/reservations/{reservationId}', methods: ['DELETE'])]
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

        $this->entityManager->remove($reservation);

        $this->entityManager->flush();

        return $this->successResponse([], 204);
    }
}