<?php

namespace App\Controller\Passenger;

use App\Dto\Passengers\CreatePassengerDto;
use App\Entity\Passenger;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\ObjectMapper\ObjectMapperInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Controller for creating a new passenger.
 */
class CreatePassengerController
{
    /**
     * Entity manager for persisting passenger entities.
     */
    private EntityManagerInterface $entityManager;

    /**
     * Validator for validating the passenger DTO.
     */
    private ValidatorInterface $validator;

    /**
     * Object mapper for mapping the passenger DTO to the Passenger entity.
     */
    private ObjectMapperInterface $objectMapper;

    /**
     * Creates a new CreatePassengerController instance.
     *
     * @param EntityManagerInterface $entityManager Entity manager for database operations.
     * @param ValidatorInterface $validator Validator for validating the DTO.
     * @param ObjectMapperInterface $objectMapper Object mapper for DTO-to-entity mapping.
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        ValidatorInterface $validator,
        ObjectMapperInterface $objectMapper
    ) {
        $this->entityManager = $entityManager;
        $this->validator = $validator;
        $this->objectMapper = $objectMapper;
    }

    /**
     * Creates a new passenger.
     *
     * @param Request $request The HTTP request containing the passenger data.
     *
     * @return JsonResponse The created passenger or a validation error response.
     */
    #[Route('/api/passengers', methods: ['POST'])]
    public function __invoke(Request $request)
    {
        $dto = new CreatePassengerDto($request);

        $errors = $this->validator->validate($dto);
        
        if (count($errors) > 0) {
            $message = "Required fields are missing.";
            
            if ($errors[0]->getMessage() == 'Invalid email address.') {
                $message = $errors[0]->getMessage();
            }

            return new JsonResponse([
                'message' => $message,
                'errors' => (string) $errors,
            ], 400);
        }
        
        $passenger = $this->objectMapper->map($dto, Passenger::class);

        $this->entityManager->persist($passenger);
        $this->entityManager->flush();

        return new JsonResponse([
            'id' => $passenger->getId(),
            'first_name' => $passenger->getFirstName(),
            'last_name' => $passenger->getLastName(),
            'email' => $passenger->getEmail()
        ], 201);
    }
}