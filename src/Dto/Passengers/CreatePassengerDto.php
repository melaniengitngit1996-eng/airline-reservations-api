<?php

namespace App\Dto\Passengers;

use App\Entity\Passenger;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\ObjectMapper\Attribute\Map;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Data required to create a passenger.
 */
class CreatePassengerDto
{
    /**
     * The passenger's first name.
     */
    #[Assert\NotBlank(message: 'Required fields are missing.')]
    #[Map(target: 'firstName')]
    public string $firstName = '';

    /**
     * The passenger's last name.
     */
    #[Assert\NotBlank(message: 'Required fields are missing.')]
    #[Map(target: 'lastName')]
    public string $lastName = '';

    /**
     * The passenger's email address.
     */
    #[Assert\NotBlank(message: 'Required fields are missing.')]
    #[Assert\Email(message: 'Invalid email address.')]
    #[Map(target: 'email')]
    public string $email = '';
}