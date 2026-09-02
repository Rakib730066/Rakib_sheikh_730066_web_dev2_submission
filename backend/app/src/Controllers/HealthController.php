<?php

namespace App\Controllers;

use App\Framework\Controller;
use App\Services\HealthService;
use App\Services\IHealthService;

class HealthController extends Controller
{
    private IHealthService $healthService;

    public function __construct()
    {
        $this->healthService = new HealthService();
    }

    public function check(): void
    {
        $this->successResponse($this->healthService->getStatus());
    }

    public function ready(): void
    {
        try {
            $this->successResponse($this->healthService->getReadiness());
        } catch (\Exception $e) {
            $this->errorResponse('Database connection failed: ' . $e->getMessage(), 503);
        }
    }
}
