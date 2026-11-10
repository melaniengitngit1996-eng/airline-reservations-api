<?php

namespace App\Entity;

use App\Repository\FlightRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Represents a flight available for reservation.
 */
#[ORM\Entity(repositoryClass: FlightRepository::class)]
#[ORM\Table(name: 'flights')]
#[UniqueEntity(fields: ['flightNumber'], message: 'Flight number already exists.')]
#[ORM\HasLifecycleCallbacks]
class Flight
{
    /**
     * The unique identifier of the flight.
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * The unique flight number.
     */
    #[ORM\Column(length: 20)]
    private ?string $flightNumber = null;

    /**
     * The total passenger capacity of the flight.
     */
    #[ORM\Column]
    private ?int $capacity = null;

    /**
     * The scheduled departure time.
     */
    #[ORM\Column]
    private ?\DateTimeImmutable $departureTime = null;

    /**
     * The scheduled arrival time.
     */
    #[ORM\Column]
    private ?\DateTimeImmutable $arrivalTime = null;

    /**
     * The airport where the flight departs from.
     */
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Airport $originAirport = null;

    /**
     * The airport where the flight arrives.
     */
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Airport $destinationAirport = null;

    /**
     * The date and time when the flight was created.
     */
    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    /**
     * The date and time when the flight was last updated.
     */
    #[ORM\Column]
    private ?\DateTimeImmutable $updatedAt = null;

    /**
     * Returns the unique identifier of the flight.
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Returns the flight number.
     */
    public function getFlightNumber(): ?string
    {
        return $this->flightNumber;
    }

    /**
     * Sets the flight number.
     */
    public function setFlightNumber(string $flightNumber): static
    {
        $this->flightNumber = $flightNumber;

        return $this;
    }

    /**
     * Returns the total passenger capacity.
     */
    public function getCapacity(): ?int
    {
        return $this->capacity;
    }

    /**
     * Sets the total passenger capacity.
     */
    public function setCapacity(int $capacity): static
    {
        $this->capacity = $capacity;

        return $this;
    }

    /**
     * Returns the scheduled departure time.
     */
    public function getDepartureTime(): ?\DateTimeImmutable
    {
        return $this->departureTime;
    }

    /**
     * Sets the scheduled departure time.
     */
    public function setDepartureTime(\DateTimeImmutable $departureTime): static
    {
        $this->departureTime = $departureTime;

        return $this;
    }

    /**
     * Returns the scheduled arrival time.
     */
    public function getArrivalTime(): ?\DateTimeImmutable
    {
        return $this->arrivalTime;
    }

    /**
     * Sets the scheduled arrival time.
     */
    public function setArrivalTime(\DateTimeImmutable $arrivalTime): static
    {
        $this->arrivalTime = $arrivalTime;

        return $this;
    }

    /**
     * Returns the origin airport.
     */
    public function getOriginAirport(): ?Airport
    {
        return $this->originAirport;
    }

    /**
     * Sets the origin airport.
     */
    public function setOriginAirport(?Airport $originAirport): static
    {
        $this->originAirport = $originAirport;

        return $this;
    }

    /**
     * Returns the destination airport.
     */
    public function getDestinationAirport(): ?Airport
    {
        return $this->destinationAirport;
    }

    /**
     * Sets the destination airport.
     */
    public function setDestinationAirport(?Airport $destinationAirport): static
    {
        $this->destinationAirport = $destinationAirport;

        return $this;
    }

    /**
     * Returns the creation date and time.
     */
    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * Sets the creation date and time.
     */
    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Returns the last update date and time.
     */
    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    /**
     * Sets the last update date and time.
     */
    public function setUpdatedAt(\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Sets the creation and update timestamps when the entity is persisted.
     */
    #[ORM\PrePersist]
    public function setTimestampsOnCreate(): void
    {
        $now = new \DateTimeImmutable();

        $this->createdAt = $now;
        $this->updatedAt = $now;
    }

    /**
     * Updates the updated timestamp before the entity is updated.
     */
    #[ORM\PreUpdate]
    public function setUpdatedAtOnUpdate(): void
    {
        $this->updatedAt = new \DateTimeImmutable();
    }
}