<?php

namespace App\Entity;

use App\Repository\ReservationPassengerRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * Represents a passenger assigned to a reservation and a seat.
 */
#[ORM\Entity(repositoryClass: ReservationPassengerRepository::class)]
#[ORM\Table(name: 'reservation_passengers')]
#[ORM\UniqueConstraint(name: 'UNIQ_RP_RESERVATION_PASSENGER', columns: ['reservation_id', 'passenger_id'])]
#[ORM\HasLifecycleCallbacks]
class ReservationPassenger
{
    /**
     * The unique identifier of the reservation passenger assignment.
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * The reservation associated with the passenger.
     */
    #[ORM\ManyToOne(inversedBy: 'reservationPassengers')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Reservation $reservation = null;

    /**
     * The seat assigned to the passenger.
     */
    #[ORM\ManyToOne(inversedBy: 'reservationPassengers')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Seat $seat = null;

    /**
     * The passenger assigned to the reservation.
     */
    #[ORM\ManyToOne(inversedBy: 'reservationPassengers')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Passenger $passenger = null;

    /**
     * The date and time when the assignment was created.
     */
    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    /**
     * Returns the unique identifier of the reservation passenger assignment.
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Returns the reservation associated with the passenger.
     */
    public function getReservation(): ?Reservation
    {
        return $this->reservation;
    }

    /**
     * Sets the reservation associated with the passenger.
     *
     * @param Reservation|null $reservation The reservation to associate.
     *
     * @return static
     */
    public function setReservation(?Reservation $reservation): static
    {
        $this->reservation = $reservation;

        return $this;
    }

    /**
     * Returns the seat assigned to the passenger.
     */
    public function getSeat(): ?Seat
    {
        return $this->seat;
    }

    /**
     * Sets the seat assigned to the passenger.
     *
     * @param Seat|null $seat The seat to assign.
     *
     * @return static
     */
    public function setSeat(?Seat $seat): static
    {
        $this->seat = $seat;

        return $this;
    }

    /**
     * Returns the date and time when the assignment was created.
     */
    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * Sets the creation date and time of the assignment.
     *
     * @param \DateTimeImmutable $createdAt The creation date and time.
     *
     * @return static
     */
    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Sets the creation timestamp automatically before persisting the entity.
     */
    #[ORM\PrePersist]
    public function setTimestampsOnCreate(): void
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    /**
     * Returns the passenger assigned to the reservation.
     */
    public function getPassenger(): ?Passenger
    {
        return $this->passenger;
    }

    /**
     * Sets the passenger assigned to the reservation.
     *
     * @param Passenger|null $passenger The passenger to assign.
     *
     * @return static
     */
    public function setPassenger(?Passenger $passenger): static
    {
        $this->passenger = $passenger;

        return $this;
    }
}