<?php

namespace App\Repositories;

interface IHealthRepository
{
    public function isDatabaseConnected(): bool;
}
