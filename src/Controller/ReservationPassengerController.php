<?php

namespace App\Controller;

use App\Service\ReservationPassengerServiceInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/reservations/{reservationId}/passengers')]
class ReservationPassengerController
{
    public function __construct(
        private ReservationPassengerServiceInterface $reservationPassengerService
    ) {
    }

    #[Route('', methods: ['GET'])]
    public function index(int $reservationId): JsonResponse
    {
        try {
            $reservationPassengers = $this->reservationPassengerService
                ->findAll($reservationId);
        } catch (\DomainException $e) {
            return new JsonResponse([
                'message' => $e->getMessage()
            ], 404);
        }

        return new JsonResponse(array_map(
            fn ($reservationPassenger) => [
                'reservation_passenger_id' => $reservationPassenger->getId(),
                'passenger_id' => $reservationPassenger->getPassenger()->getId(),
                'seat_id' => $reservationPassenger->getSeat()->getId(),
            ],
            $reservationPassengers
        ));
    }

    #[Route('/{id}', methods: ['GET'])]
    public function show(int $reservationId, int $id): JsonResponse
    {
        try {
            $reservationPassengers = $this->reservationPassengerService
                ->findAll($reservationId);

            $reservationPassenger = null;

            foreach ($reservationPassengers as $item) {
                if ($item->getId() === $id) {
                    $reservationPassenger = $item;
                    break;
                }
            }

            if (!$reservationPassenger) {
                return new JsonResponse([
                    'message' => 'Reservation passenger not found.'
                ], 404);
            }
        } catch (\DomainException $e) {
            return new JsonResponse([
                'message' => $e->getMessage()
            ], 404);
        }

        return new JsonResponse([
            'reservation_passenger_id' => $reservationPassenger->getId(),
            'passenger_id' => $reservationPassenger->getPassenger()->getId(),
            'seat_id' => $reservationPassenger->getSeat()->getId(),
        ]);
    }
}