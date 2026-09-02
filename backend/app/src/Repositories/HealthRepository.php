<?php

namespace App\Repositories;

class HealthRepository extends BaseRepository implements IHealthRepository
{
    public function isDatabaseConnected(): bool
    {
        return (bool)$this->selectOne('SELECT 1 AS ok');
    }
}
