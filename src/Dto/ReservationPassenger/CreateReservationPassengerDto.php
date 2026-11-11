<?php

namespace App\Dto\ReservationPassenger;

use Symfony\Component\Validator\Constraints as Assert;

class CreateReservationPassengerDto
{
    #[Assert\NotBlank(message: 'Passenger ID is required.')]
    #[Assert\Positive(message: 'Passenger ID is required.')]
    public int $passengerId;

    #[Assert\NotBlank(message: 'Seat ID is required.')]
    #[Assert\Positive(message: 'Seat ID is required.')]
    public int $seatId;
}