<?php

namespace App\Controller\Airport;

use App\Controller\AbstractController;
use App\Entity\Airport;
use App\Entity\Flight;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Handles deleting airports.
 */
class DeleteAirportController extends AbstractController
{
    /**
     * Deletes an existing airport.
     *
     * @param int $airportId The ID of the airport to delete.
     *
     * @return JsonResponse The success response or an error response.
     */
    #[Route('/api/airports/{airportId}', methods: ['DELETE'])]
    public function __invoke(int $airportId)
    {
        $airportRepository = $this->entityManager->getRepository(Airport::class);

        $airport = $airportRepository->find($airportId);

        if ($airport === null) {
            return $this->errorResponse(
                'Airport not found.',
                404
            );
        }

        $flightRepository = $this->entityManager->getRepository(Flight::class);

        $flights = $flightRepository->findBy([
            'originAirport' => $airport,
        ]);

        if (count($flights) > 0) {
            return $this->errorResponse(
                'This airport has existing flights.',
                409
            );
        }

        $this->entityManager->remove($airport);
        $this->entityManager->flush();

        return $this->successResponse([], 204);
    }
}