<?php

namespace App\Models;

class User
{
    public ?int $id = null;
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $role = 'user';
    public ?string $created_at = null;
    public ?string $updated_at = null;

    public function __construct(array $data = [])
    {
        $this->id = isset($data['id']) ? (int)$data['id'] : null;
        $this->name = (string)($data['name'] ?? '');
        $this->email = (string)($data['email'] ?? '');
        $this->password = (string)($data['password'] ?? '');
        $this->role = (string)($data['role'] ?? 'user');
        $this->created_at = isset($data['created_at']) ? (string)$data['created_at'] : null;
        $this->updated_at = isset($data['updated_at']) ? (string)$data['updated_at'] : null;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'role' => $this->role,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
