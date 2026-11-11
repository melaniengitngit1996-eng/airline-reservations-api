<?php

namespace App\Dto\Seat;

use App\Entity\Flight;
use App\Entity\Seat;
use Symfony\Component\ObjectMapper\Attribute\Map;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Data transfer object for creating a seat.
 *
 * Maps the incoming seat data to the Seat entity.
 */
class CreateSeatDto
{
    /**
     * The seat code.
     */
    #[Assert\NotBlank]
    #[Map(target: 'code')]
    public string $code;

    /**
     * The flight associated with the seat.
     */
    #[Map(target: 'flight')]
    public Flight $flight;
}