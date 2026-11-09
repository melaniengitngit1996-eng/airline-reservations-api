<?php

namespace App\Controller\Flight;

use App\Dto\Flight\CreateFlightDto;
use App\Entity\Flight;
use App\Repository\AirportRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\ObjectMapper\ObjectMapperInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/flights')]
class CreateFlightController {
    /**
     * Repository for retrieving airport records.
     */
    private AirportRepository $airportRepository;
    
    /**
     * Entity manager for persisting flight entities.
     */
    private EntityManagerInterface $entityManager;

    /**
     * Validator for validating the flight DTO.
     */
    private ValidatorInterface $validator;

    /**
     * Object mapper for mapping the flight DTO to the Flight entity.
     */
    private ObjectMapperInterface $objectMapper;

    /**
     * Creates a new CreateFlightController instance.
     *
     * @param AirportRepository $airportRepository Repository for retrieving airport records.
     * @param EntityManagerInterface $entityManager Entity manager for database operations.
     * @param ValidatorInterface $validator Validator for validating the DTO.
     * @param ObjectMapperInterface $objectMapper Object mapper for DTO-to-entity mapping.
     */
    public function __construct(
        AirportRepository $airportRepository,
        EntityManagerInterface $entityManager,
        ValidatorInterface $validator,
        ObjectMapperInterface $objectMapper
    ) {
        $this->airportRepository = $airportRepository;
        $this->entityManager = $entityManager;
        $this->validator = $validator;
        $this->objectMapper = $objectMapper;
    }

    /**
     * Creates a new flight.
     *
     * @param Request $request The HTTP request containing the flight data.
     *
     * @return JsonResponse The created flight or a validation error response.
     */
    #[Route('', methods: ['POST'])]
    public function __invoke(Request $request)
    {
        $data = json_decode($request->getContent(), true);
        
        if (empty($data)) {
            return new JsonResponse([
                'message' => 'Required fields are missing.'
            ], 400);
        }

        $departureAirport = null;

        if ($data['departure_airport_id']) {
            $departureAirport = $this->airportRepository->find($data['departure_airport_id']);
        }

        if (!$departureAirport) {
            return new JsonResponse([
                'message' => 'Airport not found.'
            ], 404);
        }

        $arrivalAirport = null;

        if ($data['arrival_airport_id']) {
            $arrivalAirport = $this->airportRepository->find($data['arrival_airport_id']);
        }

        if (!$arrivalAirport) {
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