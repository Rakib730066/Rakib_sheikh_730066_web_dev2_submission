<?php

namespace App\Repositories;

class StatsRepository extends BaseRepository implements IStatsRepository
{
    public function getSummary(): array
    {
        return [
            'total_events' => $this->countTable('events'),
            'total_users' => $this->countTable('users'),
            'total_registrations' => $this->countTable('registrations'),
            'upcoming_events' => $this->countUpcomingEvents(),
        ];
    }

    private function countTable(string $table): int
    {
        $allowedTables = ['events', 'users', 'registrations'];
        if (!in_array($table, $allowedTables, true)) {
            throw new \InvalidArgumentException('Invalid table name');
        }

        $row = $this->selectOne("SELECT COUNT(*) AS count FROM {$table}");
        return (int)($row['count'] ?? 0);
    }

    private function countUpcomingEvents(): int
    {
        $row = $this->selectOne('SELECT COUNT(*) AS count FROM events WHERE date > NOW()');
        return (int)($row['count'] ?? 0);
    }
}
