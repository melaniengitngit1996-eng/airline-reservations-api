<?php

namespace App\Controller\Airport;

use App\Controller\AbstractController;
use App\Entity\Airport;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Handles retrieving airports.
 */
class GetAirportController extends AbstractController
{
    /**
     * Retrieves an existing airport.
     *
     * @param int $airportId The ID of the airport to retrieve.
     *
     * @return JsonResponse The airport data or an error response.
     */
    #[Route('/api/airports/{airportId}', methods: ['GET'])]
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

        return $this->successResponse([
            'id' => $airport->getId(),
            'code' => $airport->getCode(),
            'name' => $airport->getName(),
            'city' => $airport->getCity(),
            'country' => $airport->getCountry(),
        ], 200);
    }
}