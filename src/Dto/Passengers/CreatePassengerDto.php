<?php

namespace App\Dto\Passengers;

use App\Entity\Passenger;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\ObjectMapper\Attribute\Map;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Data transfer object for creating a passenger.
 *
 * Maps the incoming passenger data to the Passenger entity.
 */
#[Map(target: Passenger::class)]
class CreatePassengerDto
{
    /**
     * The passenger's first name.
     */
    #[Assert\NotBlank]
    #[Map(target: 'firstName')]
    public string $first_name = '';

    /**
     * The passenger's last name.
     */
    #[Assert\NotBlank]
    #[Map(target: 'lastName')]
    public string $last_name = '';

    /**
     * The passenger's email address.
     */
    #[Assert\NotBlank]
    #[Assert\Email(message: 'Invalid email address.')]
    #[Map(target: 'email')]
    public string $email = '';

    /**
     * Creates a new CreatePassengerDto instance.
     *
     * @param Request $request The HTTP request containing the passenger data.
     */
    public function __construct(Request $request)
    {
        $data = json_decode($request->getContent(), true);

        $this->first_name = $data['first_name'] ?? '';
        $this->last_name = $data['last_name'] ?? '';
        $this->email = $data['email'] ?? '';
    }
}