<?php

namespace App\Dto\Reservation;

use App\Dto\ReservationPassenger\CreateReservationPassengerDto;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Data transfer object for creating a seat.
 *
 * Maps the incoming seat data to the Seat entity.
 */
class CreateReservationDto
{
    /**
     * The reserved flight.
     */
    #[Assert\NotBlank(message: 'Flight not found.')]
    public int $flightId;

    /**
     * The passengers associated with the reservation.
     */
    #[Assert\Count(min: 1, minMessage: 'Passengers not found.')]
    #[Assert\Valid]
    public array $passengers;
}