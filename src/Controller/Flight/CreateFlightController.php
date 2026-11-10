<?php

namespace App\Controller\Flight;

use App\Controller\AbstractController;
use App\Dto\Flight\CreateFlightDto;
use App\Entity\Airport;
use App\Entity\Flight;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class CreateFlightController extends AbstractController {
    /**
     * Creates a new flight.
     *
     * @param Request $request The HTTP request containing the flight data.
     *
     * @return JsonResponse The created flight or a validation error response.
     */
    #[Route('/api/flights', methods: ['POST'])]
    public function __invoke(Request $request)
    {
        $data = json_decode($request->getContent(), true);
        
        if (empty($data)) {
            return new JsonResponse([
                'message' => 'Required fields are missing.'
            ], 400);
        }

        $airportRepository = $this->entityManager->getRepository(Airport::class);

        $departureAirport = null;

        if (isset($data['departure_airport_id'])) {
            $departureAirport = $airportRepository->find($data['departure_airport_id']);
        }

        if ($departureAirport === null) {
            return new JsonResponse([
                'message' => 'Airport not found.'
            ], 404);
        }

        $arrivalAirport = null;

        if (isset($data['arrival_airport_id'])) {
            $arrivalAirport = $airportRepository->find($data['arrival_airport_id']);
        }

        if ($arrivalAirport === null) {
            return new JsonResponse([
                'message' => 'Airport not found.'
            ], 404);
        }

        $dto = new CreateFlightDto();

        $dto->flightNumber = $data['flight_number'] ?? '';
        $dto->originAirport = $departureAirport;
        $dto->destinationAirport = $arrivalAirport;
        $dto->departureTime = $data['departure_time'] ?? '';
        $dto->arrivalTime = $data['arrival_time'] ?? '';
        $dto->capacity = $data['capacity'] ?? '';

        $errors = $this->validator->validate($dto);

        if (count($errors) > 0) {
            return new JsonResponse([
                'message' => $errors[0]->getMessage(),
                'errors' => (string) $errors,
            ], 400);
        }

        $flight = $this->objectMapper->map($dto, Flight::class);

        $errors = $this->validator->validate($flight);
        
        if (count($errors) > 0) {
            return new JsonResponse([
                'message' => $errors[0]->getMessage(),
                'errors' => (string) $errors,
            ], 409);
        }

        $this->entityManager->persist($flight);
        $this->entityManager->flush();

        return new JsonResponse([
            'id' => $flight->getId(),
            'flight_number' => $flight->getFlightNumber(),
            'departure_airport_id' => $flight->getOriginAirport()->getId(),
            'arrival_airport_id' => $flight->getDestinationAirport()->getId(),
            'departure_time' => $flight->getDepartureTime(),
            'arrival_time' => $flight->getArrivalTime(),
            'capacity' => $flight->getCapacity()
        ],201);
    }
}