<?php

namespace App\Entity;

use App\Repository\SeatRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Represents a seat assigned to a specific flight.
 *
 * A seat is identified by its code within a flight and can be assigned
 * to reservation passengers.
 */
#[ORM\Entity(repositoryClass: SeatRepository::class)]
#[ORM\Table(name: 'seats')]
#[ORM\UniqueConstraint(name: 'UNIQ_SEAT_FLIGHT_CODE', columns: ['flight_id', 'code'])]
#[UniqueEntity(fields: ['code'], message: 'The seat code already exists for this flight.')]
#[ORM\HasLifecycleCallbacks]
class Seat
{
    /**
     * The unique identifier of the seat.
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * The flight to which this seat belongs.
     */
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Flight $flight = null;

    /**
     * The seat code.
     */

    #[ORM\Column(length: 10)]
    private ?string $code = null;

    /**
     * The date and time when the seat was created.
     */
    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    /**
     * Reservation passengers assigned to this seat.
     *
     * @var Collection<int, ReservationPassenger>
     */
    #[ORM\OneToMany(targetEntity: ReservationPassenger::class, mappedBy: 'seat')]
    private Collection $reservationPassengers;

    /**
     * Initializes the seat entity.
     */
    public function __construct()
    {
        $this->reservationPassengers = new ArrayCollection();
    }

    /**
     * Gets the seat ID.
     *
     * @return int|null The seat ID, or null if not yet persisted.
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Gets the flight associated with this seat.
     *
     * @return Flight|null The associated flight.
     */
    public function getFlight(): ?Flight
    {
        return $this->flight;
    }

    /**
     * Sets the flight associated with this seat.
     *
     * @param Flight $flight The flight to associate with the seat.
     *
     * @return static The current Seat instance.
     */
    public function setFlight(Flight $flight): static
    {
        $this->flight = $flight;

        return $this;
    }

    /**
     * Gets the seat code.
     *
     * @return string|null The seat code.
     */
    public function getCode(): ?string
    {
        return $this->code;
    }

    /**
     * Sets the seat code.
     *
     * @param string $code The seat code.
     *
     * @return static The current Seat instance.
     */
    public function setCode(string $code): static
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Gets the creation date and time.
     *
     * @return \DateTimeImmutable|null The creation timestamp.
     */
    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * Sets the creation date and time.
     *
     * @param \DateTimeImmutable $createdAt The creation timestamp.
     *
     * @return static The current Seat instance.
     */
    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Sets the creation timestamp before the entity is persisted.
     */
    #[ORM\PrePersist]
    public function setTimestampsOnCreate(): void
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    /**
     * Gets the reservation passengers assigned to this seat.
     *
     * @return Collection<int, ReservationPassenger> The reservation passengers.
     */
    public function getReservationPassengers(): Collection
    {
        return $this->reservationPassengers;
    }

    /**
     * Adds a reservation passenger to this seat.
     *
     * @param ReservationPassenger $reservationPassenger The reservation passenger to add.
     *
     * @return static The current Seat instance.
     */

    public function addReservationPassenger(ReservationPassenger $reservationPassenger): static
    {
        if (!$this->reservationPassengers->contains($reservationPassenger)) {
            $this->reservationPassengers->add($reservationPassenger);
            $reservationPassenger->setSeat($this);
        }

        return $this;
    }

    /**
     * Removes a reservation passenger from this seat.
     *
     * @param ReservationPassenger $reservationPassenger The reservation passenger to remove.
     *
     * @return static The current Seat instance.
     */
    public function removeReservationPassenger(ReservationPassenger $reservationPassenger): static
    {
        if ($this->reservationPassengers->removeElement($reservationPassenger)) {
            // set the owning side to null (unless already changed)
            if ($reservationPassenger->getSeat() === $this) {
                $reservationPassenger->setSeat(null);
            }
        }

        return $this;
    }
}
