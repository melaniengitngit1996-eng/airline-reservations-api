<?php

namespace App\Entity;

use App\Repository\AirportRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Represents an airport.
 *
 * Stores airport information such as its code, name, city, and country.
 */
#[ORM\Entity(repositoryClass: AirportRepository::class)]
#[ORM\Table(name: 'airports')]
#[ORM\UniqueConstraint(name: 'UNIQ_AIRPORT_CODE', columns: ['code'])]
#[ORM\HasLifecycleCallbacks]
#[UniqueEntity(fields: ['code'], message: 'Airport code already exists.')]
class Airport
{
    /**
     * The unique identifier of the airport.
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * The unique three-letter IATA airport code.
     */
    #[ORM\Column(length: 3, unique: true)]
    private ?string $code = null;

    /**
     * The name of the airport.
     */
    #[ORM\Column(length: 255)]
    private ?string $name = null;

    /**
     * The city where the airport is located.
     */
    #[ORM\Column(length: 100)]
    private ?string $city = null;

    /**
     * The country where the airport is located.
     */
    #[ORM\Column(length: 100)]
    private ?string $country = null;

    /**
     * The date and time when the airport was created.
     */
    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    /**
     * The date and time when the airport was last updated.
     */
    #[ORM\Column]
    private ?\DateTimeImmutable $updatedAt = null;

     /**
     * Gets the airport ID.
     *
     * @return int|null The airport ID, or null if not yet persisted.
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Gets the airport code.
     *
     * @return string|null The airport code.
     */
    public function getCode(): ?string
    {
        return $this->code;
    }

    /**
     * Sets the airport code.
     *
     * @param string $code The three-letter airport code.
     *
     * @return static The current Airport instance.
     */
    public function setCode(string $code): static
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Gets the airport name.
     *
     * @return string|null The airport name.
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * Sets the airport name.
     *
     * @param string $name The airport name.
     *
     * @return static The current Airport instance.
     */
    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Gets the city where the airport is located.
     *
     * @return string|null The city name.
     */
    public function getCity(): ?string
    {
        return $this->city;
    }

    /**
     * Sets the city where the airport is located.
     *
     * @param string $city The city name.
     *
     * @return static The current Airport instance.
     */
    public function setCity(string $city): static
    {
        $this->city = $city;

        return $this;
    }

    /**
     * Gets the country where the airport is located.
     *
     * @return string|null The country name.
     */
    public function getCountry(): ?string
    {
        return $this->country;
    }

    /**
     * Sets the country where the airport is located.
     *
     * @param string $country The country name.
     *
     * @return static The current Airport instance.
     */
    public function setCountry(string $country): static
    {
        $this->country = $country;

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
     * @return static The current Airport instance.
     */
    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Gets the last updated date and time.
     *
     * @return \DateTimeImmutable|null The last update timestamp.
     */
    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    /**
     * Sets the last updated date and time.
     *
     * @param \DateTimeImmutable $updatedAt The last update timestamp.
     *
     * @return static The current Airport instance.
     */
    public function setUpdatedAt(\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Sets the creation and update timestamps before the entity is persisted.
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
