<?php

namespace App\Service;

use App\Entity\Flight;
use App\Entity\Seat;

interface SeatServiceInterface {
    public function create(int $flightId, array $data): Seat;

    public function findByFlight(int $flightId): array;

    public function find(int $flightId, int $id);

    public function update(int $flightId, int $id, array $data): ?Seat;

    public function delete(int $flightId, int $id): bool;

    public function isTaken(Seat $seat): bool;
}