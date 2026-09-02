<?php

namespace App\Controllers;

use App\Framework\Controller;
use App\Services\EventService;
use App\Services\IEventService;
use App\Services\IRegistrationService;
use App\Services\RegistrationService;
use App\Middleware\AuthMiddleware;

class EventController extends Controller
{
    private IEventService $eventService;
    private IRegistrationService $registrationService;
    private AuthMiddleware $authMiddleware;

    public function __construct()
    {
        $this->eventService = new EventService();
        $this->registrationService = new RegistrationService();
        $this->authMiddleware = new AuthMiddleware();
    }

    public function getAll(): void
    {
        try {
            $filters = [];
            if (!empty($_GET['search'])) {
                $filters['search'] = $_GET['search'];
            }
            if (!empty($_GET['category'])) {
                $filters['category'] = $_GET['category'];
            }

            // rate limit
            $limit = min(max(1, intval($_GET['limit'] ?? 10)), 100);
            $offset = max(0, intval($_GET['offset'] ?? 0));
            $page = intdiv($offset, $limit) + 1;

            $events = $this->eventService->getAllEventsWithFilters($filters, $limit, $offset);
            $total = $this->eventService->getEventCount($filters);

            $this->paginatedResponse($events, $total, $page, $limit);

        } catch (\Exception $e) {
            $this->errorResponse('Failed to retrieve events', 500);
        }
    }

    public function get(array $vars): void
    {
        try {
            $id = (int)($vars['id'] ?? 0);
            
            if ($id <= 0) {
                $this->errorResponse('Invalid event ID', 400);
                return;
            }

            $event = $this->eventService->getEventById($id);

            if (!$event) {
                $this->errorResponse('Event not found', 404);
                return;
            }

            $this->successResponse($event, 200);

        } catch (\Exception $e) {
            $this->errorResponse('Failed to retrieve event: ' . $e->getMessage(), 500);
        }
    }

    public function create(): void
    {
        try {
            $userData = $this->authMiddleware->requireAdmin();

            $data = $this->getJsonInput();
            $data['created_by'] = $userData['id'] ?? null;
            $event = $this->eventService->createEvent($data);

            $this->successResponse(
                $event,
                201,
                'Event created successfully'
            );
        } catch (\InvalidArgumentException $e) {
            $this->errorResponse('Validation error: ' . $e->getMessage(), 422);
        } catch (\RuntimeException $e) {
            $this->authExceptionResponse($e);
        } catch (\Exception $e) {
            $this->errorResponse('Failed to create event: ' . $e->getMessage(), 500);
        }
    }

    public function update(array $vars): void
    {
        try {
            $this->authMiddleware->requireAdmin();

            $id = (int)($vars['id'] ?? 0);
            if ($id <= 0) {
                $this->errorResponse('Invalid event ID', 400);
                return;
            }

            $existing = $this->eventService->getEventById($id);
            if (!$existing) {
                $this->errorResponse('Event not found', 404);
                return;
            }

            $data = $this->getJsonInput();
            $event = $this->eventService->updateEvent($id, $data);

            $this->successResponse(
                $event,
                200,
                'Event updated successfully'
            );
        } catch (\InvalidArgumentException $e) {
            $this->errorResponse('Validation error: ' . $e->getMessage(), 422);
        } catch (\RuntimeException $e) {
            $this->authExceptionResponse($e);
        } catch (\Exception $e) {
            $this->errorResponse('Failed to update event: ' . $e->getMessage(), 500);
        }
    }

    public function delete(array $vars): void
    {
        try {
            $this->authMiddleware->requireAdmin();

            $id = (int)($vars['id'] ?? 0);
            if ($id <= 0) {
                $this->errorResponse('Invalid event ID', 400);
                return;
            }

            $deleted = $this->eventService->deleteEvent($id);
            if (!$deleted) {
                $this->errorResponse('Event not found', 404);
                return;
            }

            $this->successResponse(
                null,
                200,
                'Event deleted successfully'
            );
        } catch (\RuntimeException $e) {
            $this->authExceptionResponse($e);
        } catch (\Exception $e) {
            $this->errorResponse('Failed to delete event: ' . $e->getMessage(), 500);
        }
    }

    /**
     * GET /api/events/{id}/registrations
     * Get all registrations for a specific event (admin only)
     * 
     * Authorization: Requires valid JWT token with admin role
     * Query Parameters:
     * - limit (int): Results per page
     * - offset (int): Results to skip
     * 
     * Response: {
     *   "success": true,
     *   "status_code": 200,
     *   "data": [{id, user_id, event_id, user: {id, name, email}, ...}],
     *   "pagination": {total, total_pages, page, limit}
     * }
     */
    public function getRegistrations(array $vars): void
    {
        try {
            // authenticate
            $this->authMiddleware->requireAdmin();

            $eventId = (int)($vars['id'] ?? 0);
            if ($eventId <= 0) {
                $this->errorResponse('Invalid event ID', 400);
                return;
            }

            // check exists
            if (!$this->eventService->getEventById($eventId)) {
                $this->errorResponse('Event not found', 404);
                return;
            }

            $limit = max(1, intval($_GET['limit'] ?? 10));
            $offset = max(0, intval($_GET['offset'] ?? 0));
            $page = intdiv($offset, $limit) + 1;

            // fetch
            $registrations = $this->registrationService->getEventRegistrations($eventId, $limit, $offset);
            $total = $this->registrationService->getEventRegistrationCount($eventId);

            $this->paginatedResponse(
                array_map(fn($reg) => $reg, $registrations),
                $total,
                $page,
                $limit
            );

        } catch (\RuntimeException $e) {
            $this->authExceptionResponse($e);
        } catch (\Exception $e) {
            $this->errorResponse('Failed to retrieve registrations: ' . $e->getMessage(), 500);
        }
    }
}
