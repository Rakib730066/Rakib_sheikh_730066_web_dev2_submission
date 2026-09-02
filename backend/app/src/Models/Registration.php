<?php

namespace App\Models;

class Registration
{
    public ?int $id = null;
    public int $user_id = 0;
    public int $event_id = 0;
    public ?string $registered_at = null;
    public ?array $user = null;        // from join
    public ?array $event = null;       // from join
    public ?string $user_name = null;  // flattened for existing admin UI
    public ?string $email = null;      // flattened for existing admin UI

    public function __construct(array $data = [])
    {
        $this->id = isset($data['id']) ? (int)$data['id'] : null;
        $this->user_id = (int)($data['user_id'] ?? 0);
        $this->event_id = (int)($data['event_id'] ?? 0);
        $this->registered_at = isset($data['registered_at']) ? (string)$data['registered_at'] : null;
        $this->user = isset($data['user']) ? (array)$data['user'] : null;
        $this->event = isset($data['event']) ? (array)$data['event'] : null;
        $this->user_name = isset($data['user_name']) ? (string)$data['user_name'] : null;
        $this->email = isset($data['email']) ? (string)$data['email'] : null;
    }

    public function toArray(): array
    {
        $data = [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'event_id' => $this->event_id,
            'registered_at' => $this->registered_at,
        ];
        
        if ($this->user !== null) {
            $data['user'] = $this->user;
        }
        if ($this->event !== null) {
            $data['event'] = $this->event;
        }
        if ($this->user_name !== null) {
            $data['user_name'] = $this->user_name;
        }
        if ($this->email !== null) {
            $data['email'] = $this->email;
        }
        
        return $data;
    }
}
