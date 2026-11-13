<?php

namespace App\Controller\Seat;

use App\Controller\AbstractController;
use App\Entity\Flight;
use App\Entity\Seat;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class GetSeatsController extends AbstractController
{
    /**
     * Get all seats assigned to a flight.
     */
    #[Route('/api/flights/{flightId}/seats', methods: ['GET'])]
    public function __invoke(int $flightId): JsonResponse
    {
        $flightRepository = $this->entityManager->getRepository(Flight::class);
        $flight = $flightRepository->find($flightId);

        if ($flight === null) {
            return $this->errorResponse('Flight not found.', 404);
        }

        $seatRepository = $this->entityManager->getRepository(Seat::class);
        $seats = $seatRepository->findBy(['flight' => $flight]);

        return $this->successResponse(
            array_map(function ($seat) {
                return [
                    'id' => $seat->getId(),
                    'flight_id' => $seat->getFlight()->getId(),
                    'code' => $seat->getCode(),
                    'taken' => $this->isTaken($seat)
                ];
            }, $seats),
            200
        );
    }
}