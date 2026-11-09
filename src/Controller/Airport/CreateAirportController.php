<?php

namespace App\Controller\Airport;

use App\Dto\Airport\CreateAirportDto;
use App\Entity\Airport;
use App\Repository\AirportRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\ObjectMapper\ObjectMapperInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Controller for creating a new airport.
 */
#[Route('/api/airports')]
class CreateAirportController
{
    /**
     * Entity manager for persisting airport entities.
     */
    private EntityManagerInterface $entityManager;

    /**
     * Validator for validating airport DTOs and entities.
     */
    private ValidatorInterface $validator;

    /**
     * Object mapper for mapping the airport DTO to the Airport entity.
     */
    private ObjectMapperInterface $objectMapper;

    /**
     * Creates a new CreateAirportController instance.
     *
     * @param EntityManagerInterface $entityManager Entity manager for database operations.
     * @param ValidatorInterface $validator Validator for validating the DTO and entity.
     * @param ObjectMapperInterface $objectMapper Object mapper for DTO-to-entity mapping.
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        ValidatorInterface $validator,
        ObjectMapperInterface $objectMapper
    )
    {
        $this->entityManager = $entityManager;
        $this->validator = $validator;
        $this->objectMapper = $objectMapper;
    }

    /**
     * Creates a new airport.
     *
     * @param Request $request The HTTP request containing the airport data.
     *
     * @return JsonResponse The created airport or a validation error response.
     */
    #[Route('', methods: ['POST'])]
    public function __invoke(Request $request)
    {
        $dto = new CreateAirportDto($request);

        $errors = $this->validator->validate($dto);
        
        if (count($errors) > 0) {
            return new JsonResponse([
                'message' => 'Required fields are missing.',
                'errors' => (string) $errors,
            ], 400);
        }

        $airport = $this->objectMapper->map($dto, Airport::class);

        $errors = $this->validator->validate($airport);

        if (count($errors) > 0) {
            if ($errors[0]->getMessage() == "This airport code already exists.") {
                return new JsonResponse([
                    'message' => 'Airport code already exists.', 
                    'errors' => (string) $errors,
                ],
                409);
            }
        }

        $this->entityManager->persist($airport);
        $this->entityManager->flush();

        return new JsonResponse([
            'id' => $airport->getId(),
            'code' => $airport->getCode(),
            'name' => $airport->getName(),
            'city' => $airport->getCity(),
            'Country' => $airport->getCountry()
        ], 201);
    }
}