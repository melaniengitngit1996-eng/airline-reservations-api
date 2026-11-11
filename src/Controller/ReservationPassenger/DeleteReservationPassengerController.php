<?php

namespace App\Controller\ReservationPassenger;

use App\Controller\AbstractController;
use App\Entity\Reservation;
use App\Entity\ReservationPassenger;
use Symfony\Component\Routing\Attribute\Route;

class DeleteReservationPassengerController extends AbstractController {
    #[Route('/api/reservations/{reservationId}/passengers/{passengerId}')]
    public function __invoke(int $reservationId, int $passengerId)
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

        $this->entityManager->remove($reservationPassenger);

        $this->entityManager->flush();

        return $this->successResponse([
            'message' => 'Passenger assignment removed successfully.'
        ], 200);
    }
}