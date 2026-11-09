<?php

namespace App\Dto\Flight;

use App\Entity\Airport;
use App\Entity\Flight;
use DateTimeImmutable;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\ObjectMapper\Attribute\Map;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Data transfer object for creating a flight.
 *
 * Maps the incoming flight data to the Flight entity.
 */
#[Map(target: Flight::class)]
class CreateFlightDto {
    /**
     * The flight's flight number.
     */
    #[Map(target: 'flightNumber')]
    #[Assert\NotBlank(message: 'Required fields are missing.')]
    public string $flightNumber;

    /**
     * The flight's capacity.
     */
    #[Map(target: 'capacity')]
    #[Assert\NotBlank(message: 'Required fields are missing.')]
    public int $capacity;

    /**
     * The flight's departure time.
     */
    #[Map(
        target: 'departureTime',
        transform: [self::class, 'toDateTimeImmutable']
    )]
    #[Assert\DateTime(format: 'Y-m-d\TH:i:sP', message: 'Invalid departure time.')]
    #[Assert\NotBlank(message: 'Required fields are missing.')]
    public string $departureTime;

    /**
     * The flight's arrival time.
     */
    #[Map(
        target: 'arrivalTime',
        transform: [self::class, 'toDateTimeImmutable']
    )]
    #[Assert\DateTime(format: 'Y-m-d\TH:i:sP', message: 'Invalid arrival time.')]
    #[Assert\NotBlank(message: 'Required fields are missing.')]
    public string $arrivalTime;

    /**
     * The flight's origin airport.
     */
    #[Map(target: 'originAirport')]
    #[Assert\NotNull]
    public Airport $originAirport;

    /**
     * The flight's destination airport.
     */
    #[Map(target: 'destinationAirport')]
    #[Assert\NotNull]
    public Airport $destinationAirport;

    public static function toDateTimeImmutable(string $value): DateTimeImmutable
    {
        return new DateTimeImmutable($value);
    }
}