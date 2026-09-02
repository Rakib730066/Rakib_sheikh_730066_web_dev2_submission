<?php

namespace App\Services;

interface IEventService
{
    public function getAllEventsWithFilters(array $filters = [], int $limit = 10, int $offset = 0): array;
    public function getEventCount(array $filters = []): int;
    public function getEventById(int $id): ?array;
    public function createEvent(array $data): array;
    public function updateEvent(int $id, array $data): ?array;
    public function deleteEvent(int $id): bool;
}
