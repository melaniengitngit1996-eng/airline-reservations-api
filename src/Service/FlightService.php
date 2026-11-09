<?php

namespace App\Service;

use App\Entity\Flight;
use App\Repository\AirportRepository;
use App\Repository\FlightRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;

class FlightService implements FlightServiceInterface {
    public function __construct(
        private AirportRepository $airportRepository,
        private FlightRepository $flightRepository,
        private EntityManagerInterface $entityManager
    ) {
    }

    public function create(array $data): ?Flight 
    {
        $originAirport = $this->airportRepository->find($data['departure_airport_id']);
        $destinationAirport = $this->airportRepository->find($data['arrival_airport_id']);
        $departureTime = new DateTimeImmutable($data['departure_time']);
        $arrivalTime = new DateTimeImmutable($data['arrival_time']);

        if (empty($originAirport) || empty($destinationAirport)) {
            return null;
        }

        $existingAirport = $this->flightRepository->findOneBy([
            'flightNumber' => $data['flight_number'],
        ]);

        if ($existingAirport) {
            throw new \DomainException('Flight number already exists.');
        }

        $flight = new Flight();

        $flight->setFlightNumber($data['flight_number']);
        $flight->setCapacity($data['capacity']);
        $flight->setOriginAirport($originAirport);
        $flight->setDestinationAirport($destinationAirport);
        $flight->setDepartureTime($departureTime);
        $flight->setArrivalTime($arrivalTime);

        $this->entityManager->persist($flight);
        $this->entityManager->flush();

        return $flight;
    }

    public function findAll(): array
    {
        return $this->flightRepository->findAll();
    }

    public function find(int $id): ?Flight
    {
        return $this->flightRepository->find($id);
    }

    public function update(int $id, array $data): ?Flight
    {
        $flight = $this->find($id);

        if (!$flight) {
            return null;
        }

        if (isset($data['flight_number'])) {
            $existingFlight = $this->flightRepository->findByFlightNumber(
                $data['flight_number']
            );

            if ($existingFlight && $existingFlight->getId() !== $flight->getId()) {
                throw new \DomainException('Flight number already exists.');
            }

            $flight->setFlightNumber($data['flight_number']);
        }

        if (isset($data['departure_airport_id'])) {
            $originAirport = $this->airportRepository->find($data['departure_airport_id']);

            if (empty($originAirport)) {
                throw new \DomainException('Airport not found.');
            }

            $flight->setOriginAirport($originAirport);
        }

        if (isset($data['arrival_airport_id'])) {
            $destinationAirport = $this->airportRepository->find($data['arrival_airport_id']);

            if (empty($destinationAirport)) {
                throw new \DomainException('Airport not found.');
            }

            $flight->setOriginAirport($destinationAirport);
        }

        if (isset($data['departure_time'])) {
            $departureTime = new DateTimeImmutable($data['departure_time']);

            $flight->setDepartureTime($departureTime);
        }

        if (isset($data['arrival_time'])) {
            $arrivalTime = new DateTimeImmutable($data['arrival_time']);

            $flight->setArrivalTime($arrivalTime);
        }

        if (isset($data['capacity'])) {
            $flight->setCapacity($data['capacity']);
        }

        $this->entityManager->flush();

        return $flight;
    }

    public function delete(int $id): bool
    {
        $flight = $this->find($id);

        if (!$flight) {
            return false;
        }

        $this->entityManager->remove($flight);
        $this->entityManager->flush();

        return true;
    }
}