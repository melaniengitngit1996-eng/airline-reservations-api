<?php

namespace App\Controller\Flight;

use App\Controller\AbstractController;
use App\Dto\Flight\SearchFlightDto;
use App\Entity\Airport;
use App\Entity\Flight;
use DateTimeImmutable;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class SearchFlightController extends AbstractController {
    #[Route('/api/flights', methods: ['GET'])]
    public function __invoke(Request $request)
    {
        $data = json_decode($request->getContent(), true);

        $dto = new SearchFlightDto();
        $dto->origin = $data['origin'] ?? 0;
        $dto->destination = $data['destination'] ?? 0;
        $dto->departureDate = $data['departure_date'] ?? '';
        
        $errors = $this->validator->validate($dto);

        if (count($errors) > 0) {
            return $this->errorResponse(
                $errors[0]->getMessage(),
                400,
                (string) $errors
            );
        }

        $airportRepository = $this->entityManager->getRepository(Airport::class);

        $originAirport = $airportRepository->find($dto->origin);
        
        if ($originAirport === null) {
            return $this->errorResponse(
                'Origin airport not found.',
            400);
        }

        $destinationAirport = $airportRepository->find($dto->destination);

        if ($destinationAirport === null) {
            return $this->errorResponse(
                'Destination airport not found.',
            400);
        }
        if ($dto->origin === $dto->destination) {
            return $this->errorResponse(
                'Origin and destination airports cannot be the same.',
            400);
        }

        $flightRepository = $this->entityManager->getRepository(Flight::class);

        $flights = $flightRepository->findByRouteAndDepartureDate(
            $originAirport,
            $destinationAirport,
            new DateTimeImmutable($dto->departureDate)
        );

        return $this->successResponse(array_map(
            fn ($flight) => [
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
            ],
            $flights
        ), 200);
    }
}