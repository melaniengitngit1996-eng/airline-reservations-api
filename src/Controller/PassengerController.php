<?php

namespace App\Controller;

use App\Service\PassengerServiceInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/passengers')]
class PassengerController
{
    public function __construct(private PassengerServiceInterface $passengerService) {}

    #[Route('', methods: ['GET'])]
    public function index(Request $request): JsonResponse
    {
        $firstName = $request->query->get('first_name');
        $lastName = $request->query->get('last_name');

        $passengers = $this->passengerService->findAll($firstName, $lastName);

        return new JsonResponse(array_map(
            fn ($passenger) => [
                'passenger_id' => $passenger->getId(),
                'first_name' => $passenger->getFirstName(),
                'last_name' => $passenger->getLastName(),
                'email' => $passenger->getEmail(),
            ],
            $passengers
        ));
    }

    #[Route('/{id}', methods: ['GET'])]
    public function show(int $id): JsonResponse
    {
        $passenger = $this->passengerService->find($id);

        if (!$passenger) {
            return new JsonResponse(['message' => 'Passenger not found'], 404);
        }

        return new JsonResponse([
            'id' => $passenger->getId(),
            'first_name' => $passenger->getFirstName(),
            'last_name' => $passenger->getLastName(),
            'email' => $passenger->getEmail(),
        ]);
    }

    #[Route('/{id}', methods: ['PATCH'])]
    public function update(int $id, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $requiredFields = ['first_name', 'last_name', 'email'];

        foreach ($requiredFields as $field) {
            if (empty($data[$field])) {
                return new JsonResponse([
                    'message' => "Required fields are missing."
                ], 400);
            }
        }

        $passenger = $this->passengerService->update($id, $data);

        if (!$passenger) {
            return new JsonResponse([
                'message' => 'Passenger not found'
            ], 404);
        }

        return new JsonResponse([
            'id' => $passenger->getId(),
            'first_name' => $passenger->getFirstName(),
            'last_name' => $passenger->getLastName(),
            'email' => $passenger->getEmail(),
        ]);
    }

    #[Route('/{id}', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        if (!$this->passengerService->delete($id)) {
            return new JsonResponse(['message' => 'Passenger not found'], 404);
        }

        return new JsonResponse(null, 204);
    }
}