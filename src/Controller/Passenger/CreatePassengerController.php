<?php

namespace App\Controller\Passenger;

use App\Controller\AbstractController;
use App\Dto\Passengers\CreatePassengerDto;
use App\Entity\Passenger;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Controller for creating a new passenger.
 */
class CreatePassengerController extends AbstractController
{
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
        $data = json_decode($request->getContent(), true);

        $dto = new CreatePassengerDto($request);
        $dto->firstName = $data['first_name'] ?? '';
        $dto->lastName = $data['last_name'] ?? '';
        $dto->email = $data['email'] ?? '';

        $errors = $this->validator->validate($dto);
        
        if (count($errors) > 0) {
            return $this->errorResponse(
                $errors[0]->getMessage(),
                400,
                (string) $errors
            );
        }
        
        $passenger = $this->objectMapper->map($dto, Passenger::class);

        $this->entityManager->persist($passenger);
        $this->entityManager->flush();

        return $this->successResponse([
            'id' => $passenger->getId(),
            'first_name' => $passenger->getFirstName(),
            'last_name' => $passenger->getLastName(),
            'email' => $passenger->getEmail()
        ], 201);
    }
}