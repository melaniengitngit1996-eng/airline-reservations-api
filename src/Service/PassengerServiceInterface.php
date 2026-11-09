<?php

namespace App\Service;

use App\Entity\Passenger;

interface PassengerServiceInterface
{
    public function create(array $data): Passenger;

    public function findAll(?string $firstName = null, ?string $lastName = null): array;

    public function find(int $id): ?Passenger;

    public function update(int $id, array $data): ?Passenger;

    public function delete(int $id): bool;
}