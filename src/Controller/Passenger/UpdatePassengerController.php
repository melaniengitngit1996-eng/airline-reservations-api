<?php

namespace App\Controller\Passenger;

use App\Controller\AbstractController;
use App\Dto\Passengers\UpdatePassengerDto;
use App\Entity\Passenger;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Handles updating passengers.
 */
class UpdatePassengerController extends AbstractController
{
    /**
     * Updates an existing passenger.
     *
     * @param int $passengerId The ID of the passenger to update.
     * @param Request $request The HTTP request containing the updated passenger data.
     *
     * @return JsonResponse The updated passenger or an error response.
     */
    #[Route('/api/passengers/{passengerId}', methods: ['PATCH'])]
    public function __invoke(int $passengerId, Request $request)
    {
        $data = json_decode($request->getContent(), true);

        if (empty($data)) {
            return $this->errorResponse(
                'Required fields are missing.',
                400
            );
        }

        $dto = new UpdatePassengerDto();

        $dto->firstName = $data['first_name'] ?? null;
        $dto->lastName = $data['last_name'] ?? null;
        $dto->email = $data['email'] ?? null;

        $errors = $this->validator->validate($dto);

        if (count($errors) > 0) {
            return $this->errorResponse(
                $errors[0]->getMessage(),
                400,
                (string) $errors
            );
        }

        $passengerRepository = $this->entityManager->getRepository(
            Passenger::class
        );

        $passenger = $passengerRepository->find($passengerId);

        if ($passenger === null) {
            return $this->errorResponse(
                'Passenger not found.',
                404
            );
        }

        $passenger->setFirstName($dto->firstName);
        $passenger->setLastName($dto->lastName);
        $passenger->setEmail($dto->email);

        $errors = $this->validator->validate($passenger);

        if (count($errors) > 0) {
            return $this->errorResponse(
                $errors[0]->getMessage(),
                409,
                (string) $errors
            );
        }

        $this->entityManager->flush();

        return $this->successResponse([
            'id' => $passenger->getId(),
            'first_name' => $passenger->getFirstName(),
            'last_name' => $passenger->getLastName(),
            'email' => $passenger->getEmail(),
        ], 200);
    }
}