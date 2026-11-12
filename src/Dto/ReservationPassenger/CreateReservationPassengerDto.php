<?php

namespace App\Dto\ReservationPassenger;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Data transfer object for creating a reservation passenger assignment.
 */
class CreateReservationPassengerDto
{
    /**
     * The ID of the passenger.
     */
    #[Assert\NotBlank(message: 'Required fields are missing.')]
    #[Assert\Positive(message: 'Required fields are missing.')]
    public int $passengerId;

    /**
     * The ID of the seat assigned to the passenger.
     */
    #[Assert\NotBlank(message: 'Required fields are missing.')]
    #[Assert\Positive(message: 'Required fields are missing.')]
    public int $seatId;
}