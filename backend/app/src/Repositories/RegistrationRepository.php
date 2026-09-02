<?php

namespace App\Repositories;

use App\Models\Registration;

class RegistrationRepository extends BaseRepository implements IRegistrationRepository
{
    protected string $table = 'registrations';

    public function create(array $data): Registration
    {
        $userId = (int)($data['userId'] ?? $data['user_id'] ?? 0);
        $eventId = (int)($data['eventId'] ?? $data['event_id'] ?? 0);

        if ($userId <= 0 || $eventId <= 0) {
            throw new \InvalidArgumentException('user_id and event_id are required');
        }

        if (!$this->eventExists($eventId)) {
            throw new \InvalidArgumentException('Event not found');
        }

        if ($this->exists($userId, $eventId)) {
            throw new \InvalidArgumentException('User is already registered for this event');
        }

        $now = date('Y-m-d H:i:s');
        $sql = "INSERT INTO {$this->table} (user_id, event_id, registered_at) VALUES (:user_id, :event_id, :registered_at)";

        $id = $this->insert($sql, [
            ':user_id' => $userId,
            ':event_id' => $eventId,
            ':registered_at' => $now,
        ]);
        return $this->getRegistrationById($id);
    }

    public function getRegistrationById(int $id): Registration
    {
        $row = $this->selectOne(
            "SELECT * FROM {$this->table} WHERE id = :id",
            [':id' => $id]
        );
        
        if (!$row) {
            throw new \RuntimeException("Registration not found with id: $id");
        }
        
        return new Registration($row);
    }

    public function getByEventId(int $eventId, int $limit = 50, int $offset = 0): array
    {
        $limit = max(0, (int)$limit);
        $offset = max(0, (int)$offset);

        $sql = "SELECT r.id, r.user_id, r.event_id, r.registered_at,
                       u.id as joined_user_id, u.name as user_name, u.email as email
                FROM {$this->table} r
                JOIN users u ON r.user_id = u.id
                WHERE r.event_id = :event_id
                ORDER BY r.registered_at DESC
                LIMIT {$limit} OFFSET {$offset}";

        $rows = $this->select($sql, [
            ':event_id' => $eventId,
        ]);
        return array_map(function ($row) {
            return new Registration([
                'id' => $row['id'],
                'user_id' => $row['user_id'],
                'event_id' => $row['event_id'],
                'registered_at' => $row['registered_at'],
                'user_name' => $row['user_name'],
                'email' => $row['email'],
                'user' => [
                    'id' => $row['joined_user_id'],
                    'name' => $row['user_name'],
                    'email' => $row['email'],
                ],
            ]);
        }, $rows);
    }

    public function getCountByEvent(int $eventId): int
    {
        $row = $this->selectOne(
            "SELECT COUNT(*) as count FROM {$this->table} WHERE event_id = :event_id",
            [':event_id' => $eventId]
        );
        return (int)($row['count'] ?? 0);
    }

    public function getByUserId(int $userId, int $limit = 50, int $offset = 0): array
    {
        $limit = max(0, (int)$limit);
        $offset = max(0, (int)$offset);

        $sql = "SELECT r.id, r.user_id, r.event_id, r.registered_at,
                       e.id as event_id, e.title, e.description, e.date, 
                       e.location, e.category, e.created_by
                FROM {$this->table} r
                JOIN events e ON r.event_id = e.id
                WHERE r.user_id = :user_id
                ORDER BY r.registered_at DESC
                LIMIT {$limit} OFFSET {$offset}";

        $rows = $this->select($sql, [
            ':user_id' => $userId,
        ]);
        return array_map(function($row) {
            $data = [
                'id' => $row['id'],
                'user_id' => $row['user_id'],
                'event_id' => $row['event_id'],
                'registered_at' => $row['registered_at'],
                'event' => [
                    'id' => $row['event_id'],
                    'title' => $row['title'],
                    'description' => $row['description'],
                    'date' => $row['date'],
                    'location' => $row['location'],
                    'category' => $row['category'],
                    'created_by' => $row['created_by']
                ]
            ];
            return new Registration($data);
        }, $rows);
    }

    public function getCountByUser(int $userId): int
    {
        $row = $this->selectOne(
            "SELECT COUNT(*) as count FROM {$this->table} WHERE user_id = :user_id",
            [':user_id' => $userId]
        );
        return (int)($row['count'] ?? 0);
    }

    public function exists(int $userId, int $eventId): bool
    {
        $row = $this->selectOne(
            "SELECT id FROM {$this->table} WHERE user_id = :user_id AND event_id = :event_id LIMIT 1",
            [':user_id' => $userId, ':event_id' => $eventId]
        );
        return $row !== null;
    }

    private function eventExists(int $eventId): bool
    {
        $row = $this->selectOne(
            'SELECT id FROM events WHERE id = :event_id LIMIT 1',
            [':event_id' => $eventId]
        );
        return $row !== null;
    }

    public function deleteRegistration(int $id): bool
    {
        // check exists
        $row = $this->selectOne("SELECT id FROM {$this->table} WHERE id = :id", [':id' => $id]);
        if (!$row) {
            return false;
        }

        parent::delete("DELETE FROM {$this->table} WHERE id = :id", [':id' => $id]);
        return true;
    }

}
