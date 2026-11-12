<?php

namespace App\Controller\ReservationPassenger;

use App\Controller\AbstractController;
use App\Entity\Reservation;
use App\Entity\ReservationPassenger;
use App\Entity\Seat;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Controller for updating a passenger assignment in a reservation.
 */
class UpdateReservationPassengerController extends AbstractController
{
    /**
     * Updates the seat assigned to a passenger in a reservation.
     *
     * @param int     $reservationId The ID of the reservation.
     * @param int     $passengerId   The ID of the passenger assignment.
     * @param Request $request       The HTTP request containing the new seat ID.
     *
     * @return JsonResponse The updated passenger an error response.
     */
    #[Route('/api/reservations/{reservationId}/passengers/{passengerId}', methods: ['PATCH'])]
    public function __invoke(int $reservationId, int $passengerId, Request $request)
    {
        $reservationRepository = $this->entityManager->getRepository(Reservation::class);
        $reservation = $reservationRepository->find($reservationId);

        if ($reservation === null) {
            return $this->errorResponse(
                'Reservation not found.',
                404
            );
        }

        $reservationPassengerRepository = $this->entityManager->getRepository(ReservationPassenger::class);
        $reservationPassenger = $reservationPassengerRepository->find($passengerId);

        if ($reservationPassenger === null) {
            return $this->errorResponse(
                'Passenger not found.',
                404
            );
        }

        $data = json_decode($request->getContent(), true);

        if (isset($data['seat_id']) === false) {
            return $this->errorResponse(
                'Seat not found.',
                404
            );
        }

        $seatRepository = $this->entityManager->getRepository(Seat::class);
        $seat = $seatRepository->find($data['seat_id']);

        if ($seat === null) {
            return $this->errorResponse(
                'Seat not found.',
                404
            );
        }

        $isTaken = $reservationPassengerRepository->isTaken($seat);

        if ($isTaken === true) {
            return $this->errorResponse(
                'Seat already taken.',
                409
            );
        }

        $reservationPassenger->setSeat($seat);

        $this->entityManager->flush();

        return $this->successResponse([
            'id' => $reservationPassenger->getId(),
            'first_name' => $reservationPassenger->getPassenger()->getFirstName(),
            'last_name' => $reservationPassenger->getPassenger()->getLastName(),
            'email' => $reservationPassenger->getPassenger()->getEmail(),
            'seat' => [
                'id' => $reservationPassenger->getSeat()->getId(),
                'code' => $reservationPassenger->getSeat()->getCode(),
            ],
        ], 200);
    }
}