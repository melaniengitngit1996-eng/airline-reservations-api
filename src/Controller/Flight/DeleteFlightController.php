<?php

namespace App\Controller\Flight;

use App\Controller\AbstractController;
use App\Entity\Flight;
use App\Entity\Reservation;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

class DeleteFlightController extends AbstractController
{
    /**
     * Handles deleting flights.
     */
    #[Route('/api/flights/{flightId}', methods: ['DELETE'])]
    public function __invoke(int $flightId)
    {
        /**
         * Deletes an existing flight.
         *
         * @param int $flightId The ID of the flight to delete.
         *
         * @return JsonResponse The success response or an error response.
         */
        $flightRepository = $this->entityManager->getRepository(Flight::class);

        $flight = $flightRepository->find($flightId);

        if ($flight === null) {
            return $this->errorResponse('Flight not found.', 404);
        }

        $reservationRepository = $this->entityManager->getRepository(Reservation::class);

        $reservations = $reservationRepository->findBy([
            'flight' => $flight,
        ]);

        if (count($reservations) > 0) {
            return $this->errorResponse(
                'This flight has an existing reservations.',
                409
            );
        }

        $this->entityManager->remove($flight);
        $this->entityManager->flush();

        return $this->successResponse([], 204);
    }
}