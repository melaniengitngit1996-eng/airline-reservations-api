<?php

namespace App\Controller\Passenger;

use App\Controller\AbstractController;
use App\Entity\Passenger;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Handles deleting passengers.
 */
class DeletePassengerController extends AbstractController
{
    /**
     * Deletes an existing passenger.
     *
     * @param int $passengerId The ID of the passenger to delete.
     *
     * @return JsonResponse The success response or an error response.
     */
    #[Route('/api/passengers/{passengerId}', methods: ['DELETE'])]
    public function __invoke(int $passengerId): JsonResponse
    {
        $passengerRepository = $this->entityManager->getRepository(
            Passenger::class
        );

        $passenger = $passengerRepository->find($passengerId);

        if ($passenger === null) {
            return $this->errorResponse(
                'Passenger not found.',
                404
            );
        }

        $this->entityManager->remove($passenger);
        $this->entityManager->flush();

        return $this->successResponse([], 204);
    }
}