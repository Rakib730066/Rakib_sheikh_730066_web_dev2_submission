<?php

namespace App\Controllers;

use App\Helpers\JsonResponse;
use App\Services\IStatsService;
use App\Services\StatsService;

class StatsController
{
    private IStatsService $statsService;

    public function __construct()
    {
        $this->statsService = new StatsService();
    }

    public function summary(): void
    {
        JsonResponse::success($this->statsService->getSummary());
    }
}
