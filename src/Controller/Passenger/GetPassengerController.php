<?php

namespace App\Controller\Passenger;

use App\Controller\AbstractController;
use App\Entity\Passenger;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

class GetPassengerController extends AbstractController {
    /**
     * Get all passengers with optional name filtering.
     *
     * @param int $passengerId The passenger id.
     *
     * @return JsonResponse Returns the passenger.
     */
    #[Route('/api/passengers/{passengerId}', methods: ['GET'])]
    public function __invoke(int $passengerId)
    {
        $passengerRepository = $this->entityManager->getRepository(Passenger::class);

        $passenger = $passengerRepository->find($passengerId);

        if ($passenger === null) {
            return $this->errorResponse('Passenger not found.', 404);
        }

        return $this->successResponse([
            'id' => $passenger->getId(),
            'first_name' => $passenger->getFirstName(),
            'last_name' => $passenger->getLastName(),
            'email' => $passenger->getEmail()
        ], 200);
    }
}