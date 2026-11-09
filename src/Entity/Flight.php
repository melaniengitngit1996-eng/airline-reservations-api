<?php

namespace App\Entity;

use App\Repository\FlightRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: FlightRepository::class)]
#[ORM\Table(name: 'flights')]
#[UniqueEntity(fields: ['flightNumber'], message: 'Flight number already exists.')]
#[ORM\HasLifecycleCallbacks]
class Flight
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 20)]
    private ?string $flightNumber = null;

    #[ORM\Column]
    private ?int $capacity = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $departureTime = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $arrivalTime = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Airport $originAirport = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Airport $destinationAirport = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $updatedAt = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFlightNumber(): ?string
    {
        return $this->flightNumber;
    }

    public function setFlightNumber(string $flightNumber): static
    {
        $this->flightNumber = $flightNumber;

        return $this;
    }

    public function getCapacity(): ?int
    {
        return $this->capacity;
    }

    public function setCapacity(int $capacity): static
    {
        $this->capacity = $capacity;

        return $this;
    }

    public function getDepartureTime(): ?\DateTimeImmutable
    {
        return $this->departureTime;
    }

    public function setDepartureTime(\DateTimeImmutable $departureTime): static
    {
        $this->departureTime = $departureTime;

        return $this;
    }

    public function getArrivalTime(): ?\DateTimeImmutable
    {
        return $this->arrivalTime;
    }

    public function setArrivalTime(\DateTimeImmutable $arrivalTime): static
    {
        $this->arrivalTime = $arrivalTime;

        return $this;
    }

    public function getOriginAirport(): ?Airport
    {
        return $this->originAirport;
    }

    public function setOriginAirport(?Airport $originAirport): static
    {
        $this->originAirport = $originAirport;

        return $this;
    }

    public function getDestinationAirport(): ?Airport
    {
        return $this->destinationAirport;
    }

    public function setDestinationAirport(?Airport $destinationAirport): static
    {
        $this->destinationAirport = $destinationAirport;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    #[ORM\PrePersist]
    public function setTimestampsOnCreate(): void
    {
        $now = new \DateTimeImmutable();

        $this->createdAt = $now;
        $this->updatedAt = $now;
    }

    #[ORM\PreUpdate]
    public function setUpdatedAtOnUpdate(): void
    {
        $this->updatedAt = new \DateTimeImmutable();
    }
}
