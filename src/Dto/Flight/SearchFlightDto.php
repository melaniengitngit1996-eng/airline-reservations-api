<?php

namespace App\Dto\Flight;

use DateTimeImmutable;
use Symfony\Component\ObjectMapper\Attribute\Map;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class SearchFlightDto {
    #[Assert\NotBlank(message: 'Required fields are missing.')]
    #[Assert\Positive(message: 'Required fields are missing.')]
    public int $origin;

    #[Assert\NotBlank(message: 'Required fields are missing.')]
    #[Assert\Positive(message: 'Required fields are missing.')]
    public int $destination;

    #[Map(
        target: 'departureTime',
        transform: [self::class, 'toDateTimeImmutable']
    )]
    #[Assert\NotBlank(message: 'Required fields are missing.')]
    #[Assert\DateTime(
        format: 'Y-m-d',
        message: 'Invalid departure date.'
    )]
    #[Assert\Callback([self::class, 'validateDepartureDate'])]
    public string $departureDate;

    public static function toDateTimeImmutable(string $value): DateTimeImmutable
    {
        return new DateTimeImmutable($value);
    }

    public static function validateDepartureDate(
        string $value,
        ExecutionContextInterface $context
    ): void {
        $departureDate = DateTimeImmutable::createFromFormat('Y-m-d', $value);
        $today = new DateTimeImmutable('today');

        if ($departureDate < $today) {
            $context->buildViolation('Departure date cannot be in the past.')
                ->addViolation();
        }
    }
}