<?php

namespace App\Entity;

use App\Repository\ReservationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ReservationRepository::class)]
#[ORM\Table(name: 'reservations')]
#[ORM\HasLifecycleCallbacks]
class Reservation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Flight $flight = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    /**
     * @var Collection<int, ReservationPassenger>
     */
    #[ORM\OneToMany(targetEntity: ReservationPassenger::class, mappedBy: 'reservation')]
    private Collection $reservationPassengers;

    public function __construct()
    {
        $this->reservationPassengers = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFlight(): ?Flight
    {
        return $this->flight;
    }

    public function setFlight(Flight $flight): static
    {
        $this->flight = $flight;

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

    /**
     * @return Collection<int, ReservationPassenger>
     */
    public function getReservationPassengers(): Collection
    {
        return $this->reservationPassengers;
    }

    public function addReservationPassenger(ReservationPassenger $reservationPassenger): static
    {
        if (!$this->reservationPassengers->contains($reservationPassenger)) {
            $this->reservationPassengers->add($reservationPassenger);
            $reservationPassenger->setReservation($this);
        }

        return $this;
    }

    public function removeReservationPassenger(ReservationPassenger $reservationPassenger): static
    {
        if ($this->reservationPassengers->removeElement($reservationPassenger)) {
            // set the owning side to null (unless already changed)
            if ($reservationPassenger->getReservation() === $this) {
                $reservationPassenger->setReservation(null);
            }
        }

        return $this;
    }
}
