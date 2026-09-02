<?php

namespace App\Services;

use App\Repositories\IStatsRepository;
use App\Repositories\StatsRepository;

class StatsService implements IStatsService
{
    private IStatsRepository $statsRepository;

    public function __construct(?IStatsRepository $statsRepository = null)
    {
        $this->statsRepository = $statsRepository ?? new StatsRepository();
    }

    public function getSummary(): array
    {
        return $this->statsRepository->getSummary();
    }
}
