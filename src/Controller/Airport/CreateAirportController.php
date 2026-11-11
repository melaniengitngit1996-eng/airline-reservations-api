<?php

namespace App\Controller\Airport;

use App\Controller\AbstractController;
use App\Dto\Airport\CreateAirportDto;
use App\Entity\Airport;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Controller for creating a new airport.
 */
class CreateAirportController extends AbstractController
{
    /**
     * Creates a new airport.
     *
     * @param Request $request The HTTP request containing the airport data.
     *
     * @return JsonResponse The created airport or a validation error response.
     */
    #[Route('/api/airports', methods: ['POST'])]
    public function __invoke(Request $request)
    {
        $data = json_decode($request->getContent(), true);
        
        $dto = new CreateAirportDto();
        $dto->code = $data['code'] ?? '';
        $dto->name = $data['name'] ?? '';
        $dto->city = $data['city'] ?? '';
        $dto->country = $data['country'] ?? '';

        $errors = $this->validator->validate($dto);
        
        if (count($errors) > 0) {
            return new JsonResponse([
                'message' => $errors[0]->getMessage(),
                'errors' => (string) $errors,
            ], 400);
        }

        $airport = $this->objectMapper->map($dto, Airport::class);

        $errors = $this->validator->validate($airport);

        if (count($errors) > 0) {
            return new JsonResponse([
                'message' => $errors[0]->getMessage(),
                'errors' => (string) $errors,
            ], 409);
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