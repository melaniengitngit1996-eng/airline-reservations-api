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
            return $this->errorResponse('Required fields are missing.', 400);
        }

        $airportRepository = $this->entityManager->getRepository(Airport::class);

        $departureAirport = null;

        if (isset($data['departure_airport_id'])) {
            $departureAirport = $airportRepository->find($data['departure_airport_id']);
        }

        if ($departureAirport === null) {
            return $this->errorResponse('Airport not found.', 404);
        }

        $arrivalAirport = null;

        if (isset($data['arrival_airport_id'])) {
            $arrivalAirport = $airportRepository->find($data['arrival_airport_id']);
        }

        if ($arrivalAirport === null) {
            return $this->errorResponse('Airport not found.', 404);
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
            return $this->errorResponse(
                $errors[0]->getMessage(), 
                400,
                (string) $errors
            );
        }

        $flight = $this->objectMapper->map($dto, Flight::class);

        $errors = $this->validator->validate($flight);
        
        if (count($errors) > 0) {
            return $this->errorResponse(
                $errors[0]->getMessage(), 
                409,
                (string) $errors
            );
        }

        $this->entityManager->persist($flight);
        $this->entityManager->flush();

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
        ], 201);
    }
}