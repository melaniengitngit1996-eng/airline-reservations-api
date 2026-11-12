<?php

namespace App\Dto\ReservationPassenger;

use Symfony\Component\Validator\Constraints as Assert;

class CreateReservationPassengerDto
{
    #[Assert\NotBlank(message: 'Required fields are missing.')]
    #[Assert\Positive(message: 'Required fields are missing.')]
    public int $passengerId;

    #[Assert\NotBlank(message: 'Required fields are missing.')]
    #[Assert\Positive(message: 'Required fields are missing.')]
    public int $seatId;
}