<?php

namespace App\Controller;

use App\Service\FlightServiceInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/flights')]
class FlightController {
    public function __construct(private FlightServiceInterface $flightService) {}

    #[Route('/{id}', methods: ['GET'])]
    public function show(int $id): JsonResponse
    {
        $flight = $this->flightService->find($id);

        if (!$flight) {
            return new JsonResponse(['message' => 'Flight not found'], 404);
        }

        return new JsonResponse([
            'flight_id' => $flight->getId(),
            'flight_number' => $flight->getFlightNumber(),
            'departure_airport_id' => $flight->getDestinationAirport()->getId(),
            'arrival_airport_id' => $flight->getDestinationAirport()->getId(),
            'departure_time' => $flight->getDepartureTime()->format('Y-m-d H:i:s.u'),
            'arrival_time' => $flight->getArrivalTime()->format('Y-m-d H:i:s.u'),
            'capacity' => $flight->getCapacity(),
        ]);
    }

    #[Route('/{id}', methods: ['PATCH'])]
    public function update(int $id, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (empty($data)) {
            return new JsonResponse([
                'message' => 'At least one field is required.'
            ], 400);
        }

        try {
            $flight = $this->flightService->update($id, $data);

            if (!$flight) {
                return new JsonResponse([
                    'message' => 'Flight not found'
                ], 404);
            }
        } catch (\DomainException $e) {
            if ($e->getMessage() === 'Airport not found.') {
                return new JsonResponse([
                    'message' => $e->getMessage()
                ], 404);
            }

            return new JsonResponse([
                'message' => $e->getMessage()
            ], 409);
        }

        return new JsonResponse([
            'flight_id' => $flight->getId(),
            'flight_number' => $flight->getFlightNumber(),
            'departure_airport_id' => $flight->getDestinationAirport()->getId(),
            'arrival_airport_id' => $flight->getDestinationAirport()->getId(),
            'departure_time' => $flight->getDepartureTime()->format('Y-m-d H:i:s.u'),
            'arrival_time' => $flight->getArrivalTime()->format('Y-m-d H:i:s.u'),
            'capacity' => $flight->getCapacity(),
        ]);
    }

    #[Route('/{id}', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        if (!$this->flightService->delete($id)) {
            return new JsonResponse(['message' => 'Flight not found'], 404);
        }

        return new JsonResponse(null, 204);
    }
}