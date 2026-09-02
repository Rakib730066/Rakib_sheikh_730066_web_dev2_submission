<?php

namespace App\Services;

use App\Repositories\HealthRepository;
use App\Repositories\IHealthRepository;

class HealthService implements IHealthService
{
    private IHealthRepository $healthRepository;

    public function __construct(?IHealthRepository $healthRepository = null)
    {
        $this->healthRepository = $healthRepository ?? new HealthRepository();
    }

    public function getStatus(): array
    {
        return [
            'status' => 'API is running',
            'timestamp' => date('Y-m-d H:i:s'),
            'environment' => getenv('APP_ENV') ?: 'development',
        ];
    }

    public function getReadiness(): array
    {
        if (!$this->healthRepository->isDatabaseConnected()) {
            throw new \RuntimeException('Database connection check failed');
        }

        return [
            'status' => 'API is ready',
            'database' => 'connected',
            'timestamp' => date('Y-m-d H:i:s'),
        ];
    }
}
