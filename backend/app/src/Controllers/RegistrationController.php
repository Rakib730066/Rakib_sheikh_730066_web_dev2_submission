<?php

namespace App\Controllers;

use App\Framework\Controller;
use App\Services\IRegistrationService;
use App\Services\RegistrationService;
use App\Middleware\AuthMiddleware;

class RegistrationController extends Controller
{
    private IRegistrationService $registrationService;
    private AuthMiddleware $authMiddleware;

    public function __construct()
    {
        $this->registrationService = new RegistrationService();
        $this->authMiddleware = new AuthMiddleware();
    }

    public function create(): void
    {
        try {
            $userData = $this->authMiddleware->getAuthenticatedUser();
            if (!$userData) {
                $this->errorResponse('Unauthorized: Missing or invalid token', 401);
                return;
            }

            $data = $this->getJsonInput();
            if (empty($data['event_id'])) {
                $this->errorResponse('Validation error: event_id is required', 422);
                return;
            }

            $data['user_id'] = $userData['id'];
            $result = $this->registrationService->registerUserForEvent($data);

            $this->successResponse(
                $result,
                201,
                'Registration successful'
            );
        } catch (\InvalidArgumentException $e) {
            if ($e->getMessage() === 'Event not found') {
                $this->errorResponse('Event not found', 404);
                return;
            }

            if ($e->getMessage() === 'User is already registered for this event') {
                $this->errorResponse('User is already registered for this event', 409);
                return;
            }

            $this->errorResponse('Validation error: ' . $e->getMessage(), 422);
        } catch (\Exception $e) {
            $this->errorResponse('Failed to register: ' . $e->getMessage(), 500);
        }
    }

    public function getUserRegistrations(array $vars): void
    {
        try {
            $userData = $this->authMiddleware->getAuthenticatedUser();
            if (!$userData) {
                $this->errorResponse('Unauthorized: Missing or invalid token', 401);
                return;
            }

            $userId = (int)($vars['userId'] ?? 0);
            if ($userId <= 0) {
                $this->errorResponse('Invalid user ID', 400);
                return;
            }

            // admin permission
            if ($userData['id'] !== $userId && $userData['role'] !== 'admin') {
                $this->errorResponse('Forbidden: Cannot view other users registrations', 403);
                return;
            }

            $limit = max(1, intval($_GET['limit'] ?? 50));
            $offset = max(0, intval($_GET['offset'] ?? 0));
            $page = intdiv($offset, $limit) + 1;

            $registrations = $this->registrationService->getRegistrationsByUser($userId, $limit, $offset);
            $total = $this->registrationService->getUserRegistrationCount($userId);

            $this->paginatedResponse(
                $registrations,
                $total,
                $page,
                $limit
            );
        } catch (\Exception $e) {
            $this->errorResponse('Failed to retrieve registrations: ' . $e->getMessage(), 500);
        }
    }

    public function getByEvent(array $vars): void
    {
        try {
            $this->authMiddleware->requireAdmin();

            $eventId = (int)($vars['eventId'] ?? 0);
            if ($eventId <= 0) {
                $this->errorResponse('Invalid event ID', 400);
                return;
            }

            $limit = max(1, intval($_GET['limit'] ?? 50));
            $offset = max(0, intval($_GET['offset'] ?? 0));
            $page = intdiv($offset, $limit) + 1;

            $registrations = $this->registrationService->getEventRegistrations($eventId, $limit, $offset);
            $total = $this->registrationService->getEventRegistrationCount($eventId);

            $this->paginatedResponse($registrations, $total, $page, $limit);
        } catch (\RuntimeException $e) {
            $this->authExceptionResponse($e);
        } catch (\Exception $e) {
            $this->errorResponse('Failed to retrieve registrations: ' . $e->getMessage(), 500);
        }
    }

    public function delete(array $vars): void
    {
        try {
            $userData = $this->authMiddleware->getAuthenticatedUser();
            if (!$userData) {
                $this->errorResponse('Unauthorized: Missing or invalid token', 401);
                return;
            }

            $registrationId = (int)($vars['id'] ?? 0);
            if ($registrationId <= 0) {
                $this->errorResponse('Invalid registration ID', 400);
                return;
            }

            $registration = $this->registrationService->getRegistrationById($registrationId);
            if (!$registration) {
                $this->errorResponse('Registration not found', 404);
                return;
            }

            if ((int)$registration['user_id'] !== (int)$userData['id'] && $userData['role'] !== 'admin') {
                $this->errorResponse('Forbidden: Cannot cancel another user registration', 403);
                return;
            }

            $deleted = $this->registrationService->deleteRegistration($registrationId);

            if (!$deleted) {
                $this->errorResponse('Registration not found', 404);
                return;
            }

            $this->successResponse(
                null,
                200,
                'Registration cancelled successfully'
            );
        } catch (\Exception $e) {
            $this->errorResponse('Failed to cancel registration: ' . $e->getMessage(), 500);
        }
    }
}
