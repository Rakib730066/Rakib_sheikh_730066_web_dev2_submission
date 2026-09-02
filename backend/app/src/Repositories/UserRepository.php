<?php

namespace App\Repositories;

use App\Models\User;

class UserRepository extends BaseRepository implements IUserRepository
{
    protected string $table = 'users';

    public function create(array $data): User
    {
        try {
            if (empty($data['name']) || empty($data['email']) || empty($data['password'])) {
                throw new \InvalidArgumentException('Name, email, and password are required');
            }

            $hashedPassword = password_hash($data['password'], PASSWORD_BCRYPT);

            $id = $this->insert(
                "INSERT INTO {$this->table} (name, email, password, role) VALUES (:name, :email, :password, :role)",
                [
                    ':name' => $data['name'],
                    ':email' => $data['email'],
                    ':password' => $hashedPassword,
                    ':role' => $data['role'] ?? 'user',
                ]
            );

            $user = new User();
            $user->id = $id;
            $user->name = $data['name'];
            $user->email = $data['email'];
            $user->password = $hashedPassword;
            $user->role = $data['role'] ?? 'user';

            return $user;

        } catch (\InvalidArgumentException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw new \RuntimeException('Failed to create user');
        }
    }

    public function getUser(int $id): ?User
    {
        try {
            return $this->selectOneObject(
                "SELECT * FROM {$this->table} WHERE id = :id",
                User::class,
                [':id' => $id]
            );
        } catch (\Exception $e) {
            throw new \RuntimeException('Failed to retrieve user');
        }
    }

    public function findByEmail(string $email): ?User
    {
        try {
            if (empty($email)) {
                return null;
            }

            return $this->selectOneObject(
                "SELECT * FROM {$this->table} WHERE email = :email",
                User::class,
                [':email' => $email]
            );

        } catch (\Exception $e) {
            throw new \RuntimeException('Failed to find user by email');
        }
    }

    public function getAllUsers(int $limit = 10, int $offset = 0): array
    {
        try {
            $limit = max(0, (int)$limit);
            $offset = max(0, (int)$offset);

            return $this->selectObjects(
                "SELECT * FROM {$this->table} ORDER BY id DESC LIMIT {$limit} OFFSET {$offset}",
                User::class
            );
        } catch (\Exception $e) {
            throw new \RuntimeException('Failed to retrieve users');
        }
    }

    public function getCountUsers(): int
    {
        try {
            $result = $this->selectOne("SELECT COUNT(*) as count FROM {$this->table}");
            return (int)($result['count'] ?? 0);
        } catch (\Exception $e) {
            throw new \RuntimeException('Failed to count users');
        }
    }

    public function updateUser(int $id, array $data): ?User
    {
        $updates = [];
        $params = [':id' => $id];

        if (array_key_exists('name', $data)) {
            $updates[] = 'name = :name';
            $params[':name'] = $data['name'];
        }

        if (array_key_exists('email', $data)) {
            $updates[] = 'email = :email';
            $params[':email'] = $data['email'];
        }

        if (empty($updates)) {
            return $this->getUser($id);
        }

        $updates[] = 'updated_at = :updated_at';
        $params[':updated_at'] = date('Y-m-d H:i:s');

        $this->update(
            "UPDATE {$this->table} SET " . implode(', ', $updates) . " WHERE id = :id",
            $params
        );

        return $this->getUser($id);
    }

    public function deleteUser(int $id): bool
    {
        return $this->delete(
            "DELETE FROM {$this->table} WHERE id = :id",
            [':id' => $id]
        ) > 0;
    }
}
