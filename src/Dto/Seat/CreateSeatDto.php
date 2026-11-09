<?php

namespace App\Dto\Seat;

use App\Entity\Flight;
use App\Entity\Seat;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\ObjectMapper\Attribute\Map;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Data transfer object for creating a seat.
 *
 * Maps the incoming seat data to the Seat entity.
 */
#[Map(target: Seat::class)]
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

    /**
     * Creates a new CreateSeatDto instance.
     *
     * @param Flight  $flight  The flight associated with the seat.
     * @param Request $request The HTTP request containing the seat data.
     */
    public function __construct($flight, Request $request)
    {
        $data = json_decode($request->getContent(), true);

        $this->code = $data['code'] ?? '';

        $this->flight = $flight;
    }
}