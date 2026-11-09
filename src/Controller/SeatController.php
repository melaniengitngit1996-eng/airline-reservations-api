<?php

namespace App\Controller;

use App\Service\SeatServiceInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/flights/{flightId}/seats')]
class SeatController {
    public function __construct(private SeatServiceInterface $seatService) {}

    #[Route('', methods: ['GET'])]
    public function index(int $flightId): JsonResponse
    {
        try {
            $seats = $this->seatService->findByFlight($flightId);
        } catch (\DomainException $e) {
            return new JsonResponse([
                'message' => $e->getMessage()
            ], 404); 
        }

        return new JsonResponse(array_map(
            fn ($seat) => [
                'seat_id' => $seat->getId(),
                'flight_id' => $seat->getFlight()->getId(),
                'code' => $seat->getCode(),
                'taken' => $this->seatService->isTaken($seat),
            ],
            $seats
        ));
    }

    #[Route('/{id}', methods: ['GET'])]
    public function show(int $flightId, int $id): JsonResponse
    {
        $seat = $this->seatService->find($flightId, $id);

        if (!$seat) {
            return new JsonResponse(['message' => 'Seat not found'], 404);
        }

        return new JsonResponse([
            'seat_id' => $seat->getId(),
            'flight_id' => $seat->getFlight()->getId(),
            'code' => $seat->getCode(),
            'taken' => $this->seatService->isTaken($seat),
        ]);
    }

    #[Route('/{id}', methods: ['PATCH'])]
    public function update(int $flightId, int $id, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (empty($data)) {
            return new JsonResponse([
                'message' => 'At least one field is required.'
            ], 400);
        }

        try {
            $seat = $this->seatService->update($flightId, $id, $data);
        } catch (\DomainException $e) {
            if ($e->getMessage() === 'Seat not found.' || $e->getMessage() === 'Flight not found.') {
                return new JsonResponse([
                    'message' => $e->getMessage()
                ], 404);
            }

            return new JsonResponse([
                'message' => $e->getMessage()
            ], 409);
        }

        return new JsonResponse([
            'seat_id' => $seat->getId(),
            'flight_id' => $seat->getFlight()->getId(),
            'code' => $seat->getCode(),
            'taken' => $this->seatService->isTaken($seat),
        ]);
    }

    #[Route('/{id}', methods: ['DELETE'])]
    public function delete(int $flightId, int $id): JsonResponse
    {
        try {
            $this->seatService->delete($flightId, $id);
        } catch (\DomainException $e) {
            return new JsonResponse([
                'message' => $e->getMessage()
            ], 404);
        }

        return new JsonResponse(null, 204);
    }
}