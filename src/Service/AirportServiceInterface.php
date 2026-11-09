<?php

namespace App\Service;

use App\Entity\Airport;

interface AirportServiceInterface {
    public function create(array $data): Airport;

    public function findAll(?string $code = null, ?string $name = null): array;

    public function find(int $id): ?Airport;

    public function update(int $id, array $data): ?Airport;

    public function delete(int $id): bool;
}