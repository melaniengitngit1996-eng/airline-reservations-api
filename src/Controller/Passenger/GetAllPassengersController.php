<?php

namespace App\Controller\Passenger;

use App\Entity\Passenger;
use App\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

class GetAllPassengersController extends AbstractController
{
    /**
     * Get all passengers with optional name filtering.
     *
     * @param Request $request The HTTP request.
     *
     * @return JsonResponse Returns a list of passengers.
     */
    #[Route('/api/passengers', methods: ['GET'])]
    public function __invoke(Request $request): JsonResponse
    {
        $firstName = $request->query->get('first_name');
        $lastName = $request->query->get('last_name');

        $passengerRepository = $this->entityManager->getRepository(Passenger::class);

        if ($firstName !== null && $lastName !== null) {
            $passengers = $passengerRepository->findBy([
                'firstName' => $firstName,
                'lastName' => $lastName,
            ]);
        } elseif ($firstName !== null) {
            $passengers = $passengerRepository->findBy([
                'firstName' => $firstName,
            ]);
        } elseif ($lastName !== null) {
            $passengers = $passengerRepository->findBy([
                'lastName' => $lastName,
            ]);
        } else {
            $passengers = $passengerRepository->findAll();
        }

        return $this->successResponse(
            array_map(function (Passenger $passenger) {
                return [
                    'id' => $passenger->getId(),
                    'first_name' => $passenger->getFirstName(),
                    'last_name' => $passenger->getLastName(),
                    'email' => $passenger->getEmail(),
                ];
            }, $passengers),
            200
        );
    }
}