<?php

namespace App\Service;

use App\Entity\Flight;

interface FlightServiceInterface
{
    public function create(array $data): ?Flight;

    public function findAll(): array;

    public function find(int $id): ?Flight;

    public function update(int $id, array $data): ?Flight;

    public function delete(int $id): bool;
}