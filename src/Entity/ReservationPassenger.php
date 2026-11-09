<?php

namespace App\Entity;

use App\Repository\ReservationPassengerRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ReservationPassengerRepository::class)]
#[ORM\Table(name: 'reservation_passengers')]
#[ORM\UniqueConstraint(name: 'UNIQ_RP_RESERVATION_PASSENGER', columns: ['reservation_id', 'passenger_id'])]
#[ORM\HasLifecycleCallbacks]
class ReservationPassenger
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'reservationPassengers')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Reservation $reservation = null;

    #[ORM\ManyToOne(inversedBy: 'reservationPassengers')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Seat $seat = null;

    #[ORM\ManyToOne(inversedBy: 'reservationPassengers')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Passenger $passenger = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getReservation(): ?Reservation
    {
        return $this->reservation;
    }

    public function setReservation(?Reservation $reservation): static
    {
        $this->reservation = $reservation;

        return $this;
    }

    public function getSeat(): ?Seat
    {
        return $this->seat;
    }

    public function setSeat(?Seat $seat): static
    {
        $this->seat = $seat;

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

    #[ORM\PrePersist]
    public function setTimestampsOnCreate(): void
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getPassenger(): ?Passenger
    {
        return $this->passenger;
    }

    public function setPassenger(?Passenger $passenger): static
    {
        $this->passenger = $passenger;

        return $this;
    }
}
