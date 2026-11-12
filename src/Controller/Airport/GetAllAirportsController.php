<?php

namespace App\Controller\Airport;

use App\Controller\AbstractController;
use App\Entity\Airport;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Handles retrieving all airports.
 */
class GetAllAirportsController extends AbstractController
{
    /**
     * Retrieves all airports with code or name filters.
     *
     * @param Request $request The HTTP request containing the filter parameters.
     *
     * @return JsonResponse The list of airports or an error response.
     */
    #[Route('/api/airports', methods: ['GET'])]
    public function __invoke(Request $request)
    {
        $code = $request->query->get('code');
        $name = $request->query->get('name');

        $airportRepository = $this->entityManager->getRepository(Airport::class);

        if ($code !== null) {
            $airports = $airportRepository->findBy([
                'code' => $code,
            ]);
        } elseif ($name !== null) {
            $airports = $airportRepository->findBy([
                'name' => $name,
            ]);
        } else {
            $airports = $airportRepository->findAll();
        }

        return $this->successResponse(
            array_map(
                function ($airport) {
                    return [
                        'id' => $airport->getId(),
                        'code' => $airport->getCode(),
                        'name' => $airport->getName(),
                        'city' => $airport->getCity(),
                        'country' => $airport->getCountry(),
                    ];
                },
                $airports
            ), 200);
    }
}