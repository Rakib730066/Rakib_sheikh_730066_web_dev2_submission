<?php

namespace App\Repositories;

use App\Models\Event;

interface IEventRepository
{
    public function getAllEvents(array $filters = [], string $orderBy = 'date DESC', int $limit = 10, int $offset = 0): array;
    public function getEventById(int $id): ?Event;
    public function create(array $data): Event;
    public function updateEvent(int $id, array $data): ?Event;
    public function deleteEvent(int $id): bool;
    public function getCountEvents(array $filters = []): int;
}
