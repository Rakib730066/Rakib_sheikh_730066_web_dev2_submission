<?php

namespace App\Repositories;

use App\Models\Event;

class EventRepository extends BaseRepository implements IEventRepository
{
    protected string $table = 'events';

    public function getAllEvents(
        array $filters = [],
        string $orderBy = 'date DESC',
        int $limit = 10,
        int $offset = 0
    ): array {
        $limit = max(0, (int)$limit);
        $offset = max(0, (int)$offset);

        $sql = "SELECT e.*,
                       (SELECT COUNT(*) FROM registrations r WHERE r.event_id = e.id) AS registration_count
                FROM {$this->table} e";
        $params = [];
        $conditions = [];

        // search
        if (isset($filters['search']) && !empty($filters['search'])) {
            $conditions[] = "(title LIKE :search_title OR description LIKE :search_description)";
            $searchTerm = '%' . $filters['search'] . '%';
            $params[':search_title'] = $searchTerm;
            $params[':search_description'] = $searchTerm;
        }

        // category
        if (isset($filters['category']) && !empty($filters['category'])) {
            $conditions[] = "category = :category";
            $params[':category'] = $filters['category'];
        }

        // build query
        if (!empty($conditions)) {
            $sql .= " WHERE " . implode(" AND ", $conditions);
        }

        // pagination
        $sql .= " ORDER BY {$orderBy}";
        if ($limit > 0) {
            $sql .= " LIMIT {$limit} OFFSET {$offset}";
        }

        $rows = $this->select($sql, $params);
        return array_map(fn($row) => new Event($row), $rows);
    }


    public function getEventById(int $id): ?Event
    {
        $row = $this->selectOne(
            "SELECT e.*,
                    (SELECT COUNT(*) FROM registrations r WHERE r.event_id = e.id) AS registration_count
             FROM {$this->table} e
             WHERE e.id = :id",
            [':id' => $id]
        );
        return $row ? new Event($row) : null;
    }

    public function create(array $data): Event
    {
        // validate
        if (empty($data['title']) || empty($data['date']) || empty($data['location'])) {
            throw new \InvalidArgumentException('title, date, and location are required');
        }

        $now = date('Y-m-d H:i:s');
        $sql = "INSERT INTO {$this->table} (title, description, date, location, category, capacity, created_by, created_at, updated_at)
                VALUES (:title, :description, :date, :location, :category, :capacity, :created_by, :created_at, :updated_at)";

        $id = $this->insert($sql, [
            ':title' => $data['title'],
            ':description' => $data['description'] ?? '',
            ':date' => $data['date'],
            ':location' => $data['location'],
            ':category' => $data['category'] ?? 'General',
            ':capacity' => isset($data['capacity']) && $data['capacity'] !== '' ? (int)$data['capacity'] : null,
            ':created_by' => $data['created_by'] ?? null,
            ':created_at' => $now,
            ':updated_at' => $now,
        ]);

        return $this->getEventById($id);
    }

    public function updateEvent(int $id, array $data): ?Event
    {
        $event = $this->getEventById((int)$id);
        if (!$event) {
            return null;
        }

        $now = date('Y-m-d H:i:s');
        $updates = [];
        $params = [];

        // partial update
        if (isset($data['title'])) {
            $updates[] = "title = :title";
            $params[':title'] = $data['title'];
        }
        if (isset($data['description'])) {
            $updates[] = "description = :description";
            $params[':description'] = $data['description'];
        }
        if (isset($data['date'])) {
            $updates[] = "date = :date";
            $params[':date'] = $data['date'];
        }
        if (isset($data['location'])) {
            $updates[] = "location = :location";
            $params[':location'] = $data['location'];
        }
        if (isset($data['category'])) {
            $updates[] = "category = :category";
            $params[':category'] = $data['category'];
        }
        if (array_key_exists('capacity', $data)) {
            $updates[] = "capacity = :capacity";
            $params[':capacity'] = $data['capacity'] !== null && $data['capacity'] !== '' ? (int)$data['capacity'] : null;
        }

        // timestamp
        $updates[] = "updated_at = :updated_at";
        $params[':updated_at'] = $now;
        $params[':id'] = $id;

        if (empty($updates)) {
            return $event;
        }

        $sql = "UPDATE {$this->table} SET " . implode(", ", $updates) . " WHERE id = :id";
        parent::update($sql, $params);

        return $this->getEventById((int)$id);
    }

    public function deleteEvent(int $id): bool
    {
        $event = $this->getEventById((int)$id);
        if (!$event) {
            return false;
        }

        parent::delete("DELETE FROM {$this->table} WHERE id = :id", [':id' => $id]);
        return true;
    }

    public function getCountEvents(array $filters = []): int
    {
        $sql = "SELECT COUNT(*) as count FROM {$this->table}";
        $params = [];
        $conditions = [];

        // filters
        if (isset($filters['search']) && !empty($filters['search'])) {
            $conditions[] = "(title LIKE :search_title OR description LIKE :search_description)";
            $searchTerm = '%' . $filters['search'] . '%';
            $params[':search_title'] = $searchTerm;
            $params[':search_description'] = $searchTerm;
        }

        if (isset($filters['category']) && !empty($filters['category'])) {
            $conditions[] = "category = :category";
            $params[':category'] = $filters['category'];
        }

        if (!empty($conditions)) {
            $sql .= " WHERE " . implode(" AND ", $conditions);
        }

        $row = $this->selectOne($sql, $params);
        return (int)($row['count'] ?? 0);
    }
}
