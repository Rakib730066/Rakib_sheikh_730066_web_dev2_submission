<?php

namespace App\Models;

class Event
{
    public ?int $id = null;
    public string $title = '';
    public string $description = '';
    public string $date = '';
    public string $location = '';
    public string $category = '';
    public ?int $capacity = null;
    public int $registration_count = 0;
    public ?int $created_by = null;
    public ?string $created_at = null;
    public ?string $updated_at = null;

    public function __construct(array $data = [])
    {
        $this->id = isset($data['id']) ? (int)$data['id'] : null;
        $this->title = (string)($data['title'] ?? '');
        $this->description = (string)($data['description'] ?? '');
        $this->date = (string)($data['date'] ?? '');
        $this->location = (string)($data['location'] ?? '');
        $this->category = (string)($data['category'] ?? '');
        $this->capacity = isset($data['capacity']) ? (int)$data['capacity'] : null;
        $this->registration_count = isset($data['registration_count']) ? (int)$data['registration_count'] : 0;
        $this->created_by = isset($data['created_by']) ? (int)$data['created_by'] : null;
        $this->created_at = isset($data['created_at']) ? (string)$data['created_at'] : null;
        $this->updated_at = isset($data['updated_at']) ? (string)$data['updated_at'] : null;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'date' => $this->date,
            'location' => $this->location,
            'category' => $this->category,
            'capacity' => $this->capacity,
            'registration_count' => $this->registration_count,
            'created_by' => $this->created_by,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
