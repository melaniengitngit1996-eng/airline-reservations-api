<?php

namespace App\Service;

use App\Entity\ReservationPassenger;

interface ReservationPassengerServiceInterface {
    public function create(int $reservationId, array $data): ReservationPassenger;

    public function findAll(int $reservationId): array;

    public function update(int $reservationId, int $id, array $data): ?ReservationPassenger;

    public function delete(int $reservationId, int $id): bool;
}