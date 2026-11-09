<?php

namespace App\Controller\Seat;

use App\Controller\AbstractController;
use App\Dto\Seat\CreateSeatDto;
use App\Entity\Flight;
use App\Entity\Seat;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Controller for creating seats associated with a specific flight.
 */
#[Route('/api/flights/{flightId}/seats')]
class CreateSeatController extends AbstractController
{
    /**
     * Creates a new seat for the specified flight.
     *
     * @param int|string $flightId The ID of the flight associated with the seat.
     * @param Request $request The HTTP request containing the seat data.
     *
     * @return JsonResponse The created seat or an error response.
     */
    #[Route('', methods: ['POST'])]
    public function __invoke($flightId, Request $request)
    {
        $flightRepository = $this->entityManager
            ->getRepository(Flight::class);

        $flight = $flightRepository->find($flightId);

        if (!$flight) {
            return $this->errorResponse(
                'Flight not found.',
                404
            );
        }

        $dto = new CreateSeatDto($flight, $request);

        $errors = $this->validator->validate($dto);

        if (count($errors) > 0) {
            return $this->errorResponse(
                'Required fields are missing.',
                400,
                (string) $errors
            );
        }

        $seat = $this->objectMapper->map($dto, Seat::class);

        $errors = $this->validator->validate($seat);

        if (count($errors) > 0) {
            return $this->errorResponse(
                $errors[0]->getMessage(),
                409,
                (string) $errors
            );
        }

        $this->entityManager->persist($seat);
        $this->entityManager->flush();

        return $this->successResponse([
            'id' => $seat->getId(),
            'flight_id' => $seat->getFlight()->getId(),
            'code' => $seat->getCode(),
            'taken' => true,
        ], 201);
    }
}