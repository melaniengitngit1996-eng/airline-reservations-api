<?php

namespace App\Dto\Airport;

use App\Entity\Airport;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\ObjectMapper\Attribute\Map;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Data transfer object for creating an airport.
 *
 * Maps the incoming airport data to the Airport entity.
 */
#[Map(target: Airport::class)]
class CreateAirportDto
{
    /**
     * The airport's code.
     */
    #[Assert\NotBlank(message: 'Required fields are missing.')]
    #[Assert\Length(exactly: 3)]
    #[Map(target: 'code')]
    public string $code;

    /**
     * The airport's name.
     */
    #[Assert\NotBlank(message: 'Required fields are missing.')]
    #[Map(target: 'name')]
    public string $name;

    /**
     * The city where the airport is located.
     */
    #[Assert\NotBlank(message: 'Required fields are missing.')]
    #[Map(target: 'city')]
    public string $city;

    /**
     * The country where the airport is located.
     */
    #[Assert\NotBlank(message: 'Required fields are missing.')]
    #[Map(target: 'country')]
    public string $country;
}