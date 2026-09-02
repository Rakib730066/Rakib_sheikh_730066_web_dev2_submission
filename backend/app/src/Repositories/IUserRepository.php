<?php

namespace App\Repositories;

use App\Models\User;

interface IUserRepository
{
    public function getUser(int $id): ?User;
    public function getAllUsers(int $limit = 10, int $offset = 0): array;
    public function getCountUsers(): int;
    public function findByEmail(string $email): ?User;
    public function create(array $data): User;
    public function updateUser(int $id, array $data): ?User;
    public function deleteUser(int $id): bool;
}
