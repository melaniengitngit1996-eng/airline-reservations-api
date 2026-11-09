<?php

namespace App\Entity;

use App\Repository\PassengerRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Represents a passenger.
 *
 * Stores passenger information such as its first name, last name and email.
 */
#[ORM\Entity(repositoryClass: PassengerRepository::class)]
#[ORM\Table(name: 'passengers')]
#[ORM\HasLifecycleCallbacks]
class Passenger
{
    /**
     * The unique identifier of the passenger.
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * The passenger's first name.
     */
    #[ORM\Column(length: 255)]
    private ?string $firstName = null;

    /**
     * The passenger's last name.
     */
    #[ORM\Column(length: 255)]
    private ?string $lastName = null;

    /**
     * The passenger's email address.
     */
    #[ORM\Column(length: 255)]
    #[Assert\Email(message: 'The email address is not a valid.',)]
    private ?string $email = null;

    /**
     * The date and time when the passenger was created.
     */
    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    /**
     * The date and time when the passenger was last updated.
     */
    #[ORM\Column]
    private ?\DateTimeImmutable $updatedAt = null;

    /**
     * Passenger reservations.
     *
     * @var Collection<int, ReservationPassenger>
     */
    #[ORM\OneToMany(targetEntity: ReservationPassenger::class, mappedBy: 'passenger')]
    private Collection $reservationPassengers;

    /**
     * Initializes the passenger entity.
     */
    public function __construct()
    {
        $this->reservationPassengers = new ArrayCollection();
    }

    /**
     * Gets the passenger ID.
     *
     * @return int|null The passenger ID.
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Gets the passenger's first name.
     *
     * @return string|null The passenger's first name.
     */
    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    /**
     * Sets the passenger's first name.
     *
     * @param string $firstName The passenger's first name.
     *
     * @return static Passenger entity.
     */
    public function setFirstName(string $firstName): static
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * Gets the passenger's last name.
     *
     * @return string|null The passenger's last name.
     */
    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    /**
     * Sets the passenger's last name.
     *
     * @param string $lastName The passenger's last name.
     *
     * @return static Passenger entity.
     */
    public function setLastName(string $lastName): static
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * Gets the passenger's email address.
     *
     * @return string|null The passenger's email address.
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * Sets the passenger's email address.
     *
     * @param string $email The passenger's email address.
     *
     * @return static Passenger entity.
     */
    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Gets the passenger creation date and time.
     *
     * @return \DateTimeImmutable|null The creation timestamp.
     */
    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * Sets the passenger creation date and time.
     *
     * @param \DateTimeImmutable $createdAt The creation timestamp.
     *
     * @return static Passenger entity.
     */
    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Gets the date and time when the passenger was last updated.
     *
     * @return \DateTimeImmutable|null The update timestamp.
     */
    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    /**
     * Sets the date and time when the passenger was last updated.
     *
     * @param \DateTimeImmutable $updatedAt The update timestamp.
     *
     * @return static Passenger entity.
     */
    public function setUpdatedAt(\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Sets the creation and update timestamps before persisting
     * a new passenger entity.
     */
    #[ORM\PrePersist]
    public function setTimestampsOnCreate(): void
    {
        $now = new \DateTimeImmutable();

        $this->createdAt = $now;
        $this->updatedAt = $now;
    }

    /**
     * Updates the updated timestamp before updating
     * an existing passenger entity.
     */
    #[ORM\PreUpdate]
    public function setUpdatedAtOnUpdate(): void
    {
        $this->updatedAt = new \DateTimeImmutable();
    }

    /**
     * Gets all reservation-passenger associations for this passenger.
     *
     * @return Collection<int, ReservationPassenger>
     */
    public function getReservationPassengers(): Collection
    {
        return $this->reservationPassengers;
    }

    /**
     * Adds a reservation-passenger association to this passenger.
     *
     * @param ReservationPassenger $reservationPassenger The reservation-passenger association.
     *
     * @return static Passenger entity.
     */
    public function addReservationPassenger(ReservationPassenger $reservationPassenger): static
    {
        if (!$this->reservationPassengers->contains($reservationPassenger)) {
            $this->reservationPassengers->add($reservationPassenger);
            $reservationPassenger->setPassenger($this);
        }

        return $this;
    }

    /**
     * Removes a reservation-passenger association from this passenger.
     *
     * @param ReservationPassenger $reservationPassenger The reservation-passenger association.
     *
     * @return static Passenger entity.
     */
    public function removeReservationPassenger(ReservationPassenger $reservationPassenger): static
    {
        if ($this->reservationPassengers->removeElement($reservationPassenger)) {
            // set the owning side to null (unless already changed)
            if ($reservationPassenger->getPassenger() === $this) {
                $reservationPassenger->setPassenger(null);
            }
        }

        return $this;
    }
}
