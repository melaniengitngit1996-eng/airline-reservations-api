<?php

namespace App\Service;

use App\Entity\Airport;
use App\Repository\AirportRepository;
use Doctrine\ORM\EntityManagerInterface;

class AirportService implements AirportServiceInterface
{
    public function __construct(
        private AirportRepository $airportRepository,
        private EntityManagerInterface $entityManager
    ) {}

    public function create(array $data): Airport
    {
        $existingAirport = $this->airportRepository->findOneBy([
            'code' => $data['code'],
        ]);

        if ($existingAirport) {
            throw new \DomainException('Airport code already exists.');
        }

        $airport = new Airport();

        $airport->setCode($data['code']);
        $airport->setName($data['name']);
        $airport->setCity($data['city']);
        $airport->setCountry($data['country']);

        $this->entityManager->persist($airport);
        $this->entityManager->flush();

        return $airport;
    }

    public function findAll(?string $code = null, ?string $name = null): array
    {
        return $this->airportRepository->findByFilters($code, $name);
    }

    public function find(int $id): ?Airport
    {
        return $this->airportRepository->find($id);
    }

    public function update(int $id, array $data): ?Airport
    {
        $airport = $this->find($id);

        if (!$airport) {
            return null;
        }

        if (isset($data['code'])) {
            $airport->setCode($data['code']);
        }

        if (isset($data['name'])) {
            $airport->setName($data['name']);
        }

        if (isset($data['city'])) {
            $airport->setCity($data['city']);
        }

        if (isset($data['country'])) {
            $airport->setCountry($data['country']);
        }

        $this->entityManager->flush();

        return $airport;
    }

    public function delete(int $id): bool
    {
        $airport = $this->find($id);

        if (!$airport) {
            return false;
        }

        $this->entityManager->remove($airport);
        $this->entityManager->flush();

        return true;
    }
}