<?php

namespace App\Repositories;

interface IStatsRepository
{
    public function getSummary(): array;
}
