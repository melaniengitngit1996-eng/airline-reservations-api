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

    #[Route('/{id}', methods: ['PATCH'])]
    public function update(
        int $reservationId,
        int $id,
        Request $request
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        if (empty($data)) {
            return new JsonResponse([
                'message' => 'At least one field is required.'
            ], 400);
        }

        // Only seat_id can be updated
        if (count(array_diff(array_keys($data), ['seat_id'])) > 0) {
            return new JsonResponse([
                'message' => 'Only seat_id can be updated.'
            ], 400);
        }

        if (empty($data['seat_id'])) {
            return new JsonResponse([
                'message' => 'seat_id is required.'
            ], 400);
        }

        try {
            $reservationPassenger = $this->reservationPassengerService->update(
                $reservationId,
                $id,
                $data
            );
        } catch (\DomainException $e) {
            if ($e->getMessage() === 'Seat is already taken.') {
                return new JsonResponse([
                    'message' => $e->getMessage()
                ], 409);
            }

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

    #[Route('/{id}', methods: ['DELETE'])]
    public function delete(int $reservationId, int $id): JsonResponse
    {
        try {
            $this->reservationPassengerService->delete(
                $reservationId,
                $id
            );
        } catch (\DomainException $e) {
            return new JsonResponse([
                'message' => $e->getMessage()
            ], 404);
        }

        return new JsonResponse(null, 204);
    }
}