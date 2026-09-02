<?php

namespace App\Repositories;

use App\Models\Registration;

interface IRegistrationRepository
{
    public function create(array $data): Registration;
    public function getRegistrationById(int $id): Registration;
    public function exists(int $userId, int $eventId): bool;
    public function getByEventId(int $eventId, int $limit = 50, int $offset = 0): array;
    public function getCountByEvent(int $eventId): int;
    public function getByUserId(int $userId, int $limit = 50, int $offset = 0): array;
    public function getCountByUser(int $userId): int;
    public function deleteRegistration(int $id): bool;
}
