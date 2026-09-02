<?php

namespace App\Services;

use App\Helpers\Validator;
use App\Repositories\IUserRepository;
use App\Repositories\UserRepository;

class UserService implements IUserService
{
    private IUserRepository $userRepository;

    public function __construct(?IUserRepository $userRepository = null)
    {
        $this->userRepository = $userRepository ?? new UserRepository();
    }

    public function getProfile(int $id): ?array
    {
        return $this->getUser($id);
    }

    public function updateProfile(int $id, array $data): array
    {
        $updates = [];
        $validator = new Validator();

        if (array_key_exists('name', $data)) {
            $name = trim((string)$data['name']);
            $validator->validateRequired($name, 'name');
            $validator->validateLength($name, 2, 255, 'name');
            $updates['name'] = $name;
        }

        if (array_key_exists('email', $data)) {
            $email = trim((string)$data['email']);
            $validator->validateEmail($email);
            $updates['email'] = $email;
        }

        if (empty($updates)) {
            throw new \InvalidArgumentException('No fields to update');
        }

        if ($validator->hasErrors()) {
            throw new \InvalidArgumentException(implode(', ', $validator->getErrors()));
        }

        $user = $this->userRepository->updateUser($id, $updates);
        if (!$user) {
            throw new \RuntimeException('User not found');
        }

        return $user->toArray();
    }

    public function getUser(int $id): ?array
    {
        $user = $this->userRepository->getUser($id);
        return $user ? $user->toArray() : null;
    }

    public function getAllUsers(int $limit = 50, int $offset = 0): array
    {
        return array_map(
            fn($user) => $user->toArray(),
            $this->userRepository->getAllUsers($limit, $offset)
        );
    }

    public function getUserCount(): int
    {
        return $this->userRepository->getCountUsers();
    }

    public function deleteUser(int $id): bool
    {
        return $this->userRepository->deleteUser($id);
    }
}
