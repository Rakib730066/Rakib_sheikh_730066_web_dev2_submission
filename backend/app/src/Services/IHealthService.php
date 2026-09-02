<?php

namespace App\Services;

interface IHealthService
{
    public function getStatus(): array;
    public function getReadiness(): array;
}
