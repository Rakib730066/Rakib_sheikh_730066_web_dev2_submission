<?php

namespace App\Services;

use App\Helpers\Validator;
use App\Repositories\EventRepository;
use App\Repositories\IEventRepository;

class EventService implements IEventService
{
    private IEventRepository $eventRepository;

    public function __construct(?IEventRepository $eventRepository = null)
    {
        $this->eventRepository = $eventRepository ?? new EventRepository();
    }

    public function getAllEventsWithFilters(array $filters = [], int $limit = 10, int $offset = 0): array
    {
        $events = $this->eventRepository->getAllEvents($filters, 'date DESC', $limit, $offset);
        return array_map(fn($event) => $event->toArray(), $events);
    }

    public function getEventCount(array $filters = []): int
    {
        return $this->eventRepository->getCountEvents($filters);
    }

    public function getEventById(int $id): ?array
    {
        $event = $this->eventRepository->getEventById($id);
        return $event ? $event->toArray() : null;
    }

    public function createEvent(array $data): array
    {
        $this->validateEventData($data, true);
        $event = $this->eventRepository->create($data);
        return $event->toArray();
    }

    public function updateEvent(int $id, array $data): ?array
    {
        $this->validateEventData($data, false);
        $event = $this->eventRepository->updateEvent($id, $data);
        return $event ? $event->toArray() : null;
    }

    public function deleteEvent(int $id): bool
    {
        return $this->eventRepository->deleteEvent($id);
    }

    private function validateEventData(array $data, bool $requireAll): void
    {
        $validator = new Validator();

        if ($requireAll || array_key_exists('title', $data)) {
            $title = trim((string)($data['title'] ?? ''));
            $validator->validateRequired($title, 'title');
            $validator->validateLength($title, 2, 255, 'title');
        }

        if ($requireAll || array_key_exists('date', $data)) {
            $validator->validateRequired(trim((string)($data['date'] ?? '')), 'date');
        }

        if ($requireAll || array_key_exists('location', $data)) {
            $location = trim((string)($data['location'] ?? ''));
            $validator->validateRequired($location, 'location');
            $validator->validateLength($location, 2, 255, 'location');
        }

        if (array_key_exists('description', $data)) {
            $validator->validateLength((string)$data['description'], 0, 10000, 'description');
        }

        if (array_key_exists('category', $data)) {
            $validator->validateLength((string)$data['category'], 1, 100, 'category');
        }

        if ($validator->hasErrors()) {
            throw new \InvalidArgumentException(implode(', ', $validator->getErrors()));
        }
    }
}
