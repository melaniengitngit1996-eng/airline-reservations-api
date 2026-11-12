<?php

namespace App\Controller\Airport;

use App\Controller\AbstractController;
use App\Entity\Airport;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

/**
 *  Handles updating airports.
 */
class UpdateAirportController extends AbstractController
{
    /**
     * Updates an existing airport.
     *
     * @param int $airportId The ID of the airport to update.
     * @param Request $request The HTTP request containing the updated airport data.
     *
     * @return JsonResponse The updated airport or an error response.
     */
    #[Route('/api/airports/{airportId}', methods: ['PATCH'])]
    public function __invoke(int $airportId, Request $request)
    {
        $data = json_decode($request->getContent(), true);

        if (empty($data)) {
            return $this->errorResponse(
                'Required fields are missing.',
                400
            );
        }

        $airportRepository = $this->entityManager->getRepository(Airport::class);

        $airport = $airportRepository->find($airportId);

        if ($airport === null) {
            return $this->errorResponse(
                'Airport not found.',
                404
            );
        }

        if (isset($data['code'])) {
            $airport->setCode($data['code']);
        }

        if (isset($data['name'])) {
            $airport->setName($data['name']);
        }

        if (isset($data['city'])) {
            $airport->setCity($data['city']);
        }

        if (isset($data['country'])) {
            $airport->setCountry($data['country']);
        }

        $errors = $this->validator->validate($airport);

        if (count($errors) > 0) {
            return $this->errorResponse(
                $errors[0]->getMessage(),
                409,
                (string) $errors
            );
        }

        $this->entityManager->flush();

        return $this->successResponse([
            'id' => $airport->getId(),
            'code' => $airport->getCode(),
            'name' => $airport->getName(),
            'city' => $airport->getCity(),
            'country' => $airport->getCountry(),
        ], 200);
    }
}