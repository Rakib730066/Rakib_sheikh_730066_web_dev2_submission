<?php

namespace App\Services;

use App\Repositories\RegistrationRepository;
use App\Repositories\IRegistrationRepository;

class RegistrationService implements IRegistrationService
{
    private IRegistrationRepository $registrationRepository;

    public function __construct(?IRegistrationRepository $registrationRepository = null)
    {
        $this->registrationRepository = $registrationRepository ?? new RegistrationRepository();
    }

    public function registerUserForEvent(array $data): ?array
    {
        $registration = $this->registrationRepository->create($data);
        return $registration->toArray();
    }

    public function getEventRegistrations(int $eventId, int $limit = 50, int $offset = 0): array
    {
        $registrations = $this->registrationRepository->getByEventId($eventId, $limit, $offset);
        return array_map(fn($reg) => $reg->toArray(), $registrations);
    }

    public function getEventRegistrationCount(int $eventId): int
    {
        return $this->registrationRepository->getCountByEvent($eventId);
    }

    public function getRegistrationById(int $id): ?array
    {
        try {
            return $this->registrationRepository->getRegistrationById($id)->toArray();
        } catch (\RuntimeException $e) {
            return null;
        }
    }

    public function getRegistrationsByUser(int $userId, int $limit = 50, int $offset = 0): array
    {
        $registrations = $this->registrationRepository->getByUserId($userId, $limit, $offset);
        return array_map(fn($reg) => $reg->toArray(), $registrations);
    }

    public function getUserRegistrationCount(int $userId): int
    {
        return $this->registrationRepository->getCountByUser($userId);
    }

    public function deleteRegistration(int $id): bool
    {
        return $this->registrationRepository->deleteRegistration($id);
    }
}
