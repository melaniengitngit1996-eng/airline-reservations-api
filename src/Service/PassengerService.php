<?php

namespace App\Service;

use App\Entity\Passenger;
use App\Repository\PassengerRepository;
use Doctrine\ORM\EntityManagerInterface;

class PassengerService implements PassengerServiceInterface
{
    public function __construct(
        private PassengerRepository $passengerRepository,
        private EntityManagerInterface $entityManager
    ) {}

    public function create(array $data): Passenger
    {
        $passenger = new Passenger();

        $passenger->setFirstName($data['first_name']);
        $passenger->setLastName($data['last_name']);
        $passenger->setEmail($data['email']);

        $this->entityManager->persist($passenger);
        $this->entityManager->flush();

        return $passenger;
    }

    public function findAll(?string $firstName = null, ?string $lastName = null): array
    {
        return $this->passengerRepository->findByFilters($firstName, $lastName);
    }

    public function find(int $id): ?Passenger
    {
        return $this->passengerRepository->find($id);
    }

    public function update(int $id, array $data): ?Passenger
    {
        $passenger = $this->find($id);

        if (!$passenger) {
            return null;
        }

        if (isset($data['first_name'])) {
            $passenger->setFirstName($data['first_name']);
        }

        if (isset($data['last_name'])) {
            $passenger->setLastName($data['last_name']);
        }

        if (isset($data['email'])) {
            $passenger->setEmail($data['email']);
        }

        $this->entityManager->flush();

        return $passenger;
    }

    public function delete(int $id): bool
    {
        $passenger = $this->find($id);

        if (!$passenger) {
            return false;
        }

        $this->entityManager->remove($passenger);
        $this->entityManager->flush();

        return true;
    }
}