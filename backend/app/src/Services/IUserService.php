<?php

namespace App\Services;

interface IUserService
{
    public function getProfile(int $id): ?array;
    public function updateProfile(int $id, array $data): array;
    public function getUser(int $id): ?array;
    public function getAllUsers(int $limit = 50, int $offset = 0): array;
    public function getUserCount(): int;
    public function deleteUser(int $id): bool;
}
