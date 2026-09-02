<?php

namespace App\Services;

interface IRegistrationService
{
    public function registerUserForEvent(array $data): ?array;
    public function getEventRegistrations(int $eventId, int $limit = 50, int $offset = 0): array;
    public function getEventRegistrationCount(int $eventId): int;
    public function getRegistrationById(int $id): ?array;
    public function getRegistrationsByUser(int $userId, int $limit = 50, int $offset = 0): array;
    public function getUserRegistrationCount(int $userId): int;
    public function deleteRegistration(int $id): bool;
}
