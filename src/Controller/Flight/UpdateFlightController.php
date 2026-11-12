<?php

namespace App\Controller\Flight;

use App\Controller\AbstractController;
use App\Dto\Flight\UpdateFlightDto;
use App\Entity\Airport;
use App\Entity\Flight;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class UpdateFlightController extends AbstractController
{
    #[Route('/api/flights/{flightId}', methods: ['PATCH'])]
    public function __invoke(int $flightId, Request $request)
    {
        $data = json_decode($request->getContent(), true);

        if (empty($data)) {
            return $this->errorResponse(
                'Required fields are missing.',
                400
            );
        }

        $flightRepository = $this->entityManager->getRepository(Flight::class);

        $flight = $flightRepository->find($flightId);

        if ($flight === null) {
            return $this->errorResponse(
                'Flight not found.',
                404
            );
        }

        $airportRepository = $this->entityManager->getRepository(Airport::class);

        $departureAirport = null;

        if (isset($data['departure_airport_id'])) {
            $departureAirport = $airportRepository->find(
                $data['departure_airport_id']
            );

            if ($departureAirport === null) {
                return $this->errorResponse(
                    'Departure airport not found.',
                    404
                );
            }

            $flight->setOriginAirport($departureAirport);
        }

        $arrivalAirport = null;

        if (isset($data['arrival_airport_id'])) {
            $arrivalAirport = $airportRepository->find(
                $data['arrival_airport_id']
            );

            if ($arrivalAirport === null) {
                return $this->errorResponse(
                    'Arrival airport not found.',
                    404
                );
            }

            $flight->setDestinationAirport($arrivalAirport);
        }

        if (isset($data['flight_number'])) {
            $flight->setFlightNumber($data['flight_number']);
        }

        if (isset($data['departure_time'])) {
            $flight->setDepartureTime(
                new \DateTimeImmutable($data['departure_time'])
            );
        }

        if (isset($data['arrival_time'])) {
            $flight->setArrivalTime(
                new \DateTimeImmutable($data['arrival_time'])
            );
        }

        if (isset($data['capacity'])) {
            $flight->setCapacity($data['capacity']);
        }

        $errors = $this->validator->validate($flight);

        if (count($errors) > 0) {
            return $this->errorResponse(
                $errors[0]->getMessage(),
                409,
                (string) $errors
            );
        }

        $this->entityManager->flush();

        return $this->successResponse([
            'id' => $flight->getId(),
            'flight_number' => $flight->getFlightNumber(),
            'departure_airport' => [
                'id' => $flight->getOriginAirport()->getId(),
                'code' => $flight->getOriginAirport()->getCode(),
                'name' => $flight->getOriginAirport()->getName(),
                'city' => $flight->getOriginAirport()->getCity(),
                'country' => $flight->getOriginAirport()->getCountry(),
            ],
            'arrival_airport' => [
                'id' => $flight->getDestinationAirport()->getId(),
                'code' => $flight->getDestinationAirport()->getCode(),
                'name' => $flight->getDestinationAirport()->getName(),
                'city' => $flight->getDestinationAirport()->getCity(),
                'country' => $flight->getDestinationAirport()->getCountry(),
            ],
            'departure_time' => $flight->getDepartureTime()->format('Y-m-d H:i:s.u'),
            'arrival_time' => $flight->getArrivalTime()->format('Y-m-d H:i:s.u'),
            'capacity' => $flight->getCapacity(),
        ], 200);
    }
}