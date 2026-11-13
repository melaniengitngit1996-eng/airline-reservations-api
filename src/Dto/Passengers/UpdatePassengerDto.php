<?php

namespace App\Dto\Passengers;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Data required to update a passenger.
 */
class UpdatePassengerDto
{
    /**
     * The passenger's first name.
     */
    #[Assert\NotBlank]
    public ?string $firstName = null;

    /**
     * The passenger's last name.
     */
    #[Assert\NotBlank]
    public ?string $lastName = null;

    /**
     * The passenger's email address.
     */
    #[Assert\NotBlank]
    #[Assert\Email]
    public ?string $email = null;
}