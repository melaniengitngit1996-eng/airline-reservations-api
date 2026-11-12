<?php

namespace App\Controller\Flight;

use App\Controller\AbstractController;
use App\Entity\Flight;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Handles retrieving flights.
 */
class GetFlightController extends AbstractController
{
    /**
     * Retrieves an existing flight.
     *
     * @param int $flightId The ID of the flight to retrieve.
     *
     * @return JsonResponse The flight data or an error response.
     */
    #[Route('/api/flights/{flightId}', methods: ['GET'])]
    public function __invoke(int $flightId)
    {
        $flightRepository = $this->entityManager->getRepository(Flight::class);

        $flight = $flightRepository->find($flightId);

        if ($flight === null) {
            return $this->errorResponse('Flight not found.', 404);
        }

        return $this->successResponse([
            'id' => $flight->getId(),
            'flight_number' => $flight->getFlightNumber(),
            'departure_airport' => [
                'id' => $flight->getOriginAirport()->getId(),
                'code' => $flight->getOriginAirport()->getCode(),
                'name' => $flight->getOriginAirport()->getName(),
                'city' => $flight->getOriginAirport()->getCity(),
                'country' => $flight->getOriginAirport()->getCountry()
            ],
            'arrival_airport' => [
                'id' => $flight->getDestinationAirport()->getId(),
                'code' => $flight->getDestinationAirport()->getCode(),
                'name' => $flight->getDestinationAirport()->getName(),
                'city' => $flight->getDestinationAirport()->getCity(),
                'country' => $flight->getDestinationAirport()->getCountry()
            ],
            'departure_time' => $flight->getDepartureTime()->format('Y-m-d H:i:s.u'),
            'arrival_time' => $flight->getArrivalTime()->format('Y-m-d H:i:s.u'),
            'capacity' => $flight->getCapacity(),
        ], 200);
    }
}