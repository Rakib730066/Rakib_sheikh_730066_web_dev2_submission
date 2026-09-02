<?php

namespace App\Controllers;

use App\Framework\Controller;
use App\Middleware\AuthMiddleware;
use App\Services\IUserService;
use App\Services\UserService;

class UserController extends Controller
{
    private IUserService $userService;
    private AuthMiddleware $authMiddleware;

    public function __construct()
    {
        $this->userService = new UserService();
        $this->authMiddleware = new AuthMiddleware();
    }

    /**
     * Get current user profile
     */
    public function getProfile(): void
    {
        try {
            $userData = $this->authMiddleware->getAuthenticatedUser();
            if (!$userData) {
                $this->errorResponse('Unauthorized: Missing or invalid token', 401);
                return;
            }

            $user = $this->userService->getProfile((int)$userData['id']);
            if (!$user) {
                $this->errorResponse('User not found', 404);
                return;
            }

            $this->successResponse($user, 200, 'Profile retrieved successfully');
        } catch (\Exception $e) {
            $this->errorResponse('Failed to retrieve profile: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Update user profile
     */
    public function updateProfile(): void
    {
        try {
            $userData = $this->authMiddleware->getAuthenticatedUser();
            if (!$userData) {
                $this->errorResponse('Unauthorized: Missing or invalid token', 401);
                return;
            }

            $updatedUser = $this->userService->updateProfile(
                (int)$userData['id'],
                $this->getJsonInput()
            );
            $this->successResponse($updatedUser, 200, 'Profile updated successfully');
        } catch (\InvalidArgumentException $e) {
            $this->errorResponse('Validation error: ' . $e->getMessage(), 400);
        } catch (\Exception $e) {
            $this->errorResponse('Failed to update profile: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Get single user (admin only)
     */
    public function getUser(array $vars): void
    {
        try {
            $userData = $this->authMiddleware->getAuthenticatedUser();
            if (!$userData) {
                $this->errorResponse('Unauthorized: Missing or invalid token', 401);
                return;
            }

            // Only admins can view other users
            if ($userData['role'] !== 'admin' && (int)$userData['id'] !== (int)($vars['userId'] ?? 0)) {
                $this->errorResponse('Forbidden: Cannot view other users', 403);
                return;
            }

            $userId = (int)($vars['userId'] ?? 0);
            if ($userId <= 0) {
                $this->errorResponse('Invalid user ID', 400);
                return;
            }

            $user = $this->userService->getUser($userId);
            if (!$user) {
                $this->errorResponse('User not found', 404);
                return;
            }

            $this->successResponse($user, 200, 'User retrieved successfully');
        } catch (\Exception $e) {
            $this->errorResponse('Failed to retrieve user: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Get all users (admin only)
     */
    public function getAllUsers(): void
    {
        try {
            $this->authMiddleware->requireAdmin();

            $limit = max(1, intval($_GET['limit'] ?? 50));
            $offset = max(0, intval($_GET['offset'] ?? 0));
            $page = intdiv($offset, $limit) + 1;

            $users = $this->userService->getAllUsers($limit, $offset);
            $total = $this->userService->getUserCount();

            $this->paginatedResponse($users, $total, $page, $limit);
        } catch (\RuntimeException $e) {
            $this->authExceptionResponse($e);
        } catch (\Exception $e) {
            $this->errorResponse('Failed to retrieve users: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Delete user (admin only)
     */
    public function deleteUser(array $vars): void
    {
        try {
            $userData = $this->authMiddleware->requireAdmin();

            $userId = (int)($vars['userId'] ?? 0);
            if ($userId <= 0) {
                $this->errorResponse('Invalid user ID', 400);
                return;
            }

            // Prevent self-deletion
            if ($userId === $userData['id']) {
                $this->errorResponse('Cannot delete your own account', 400);
                return;
            }

            $user = $this->userService->getUser($userId);
            if (!$user) {
                $this->errorResponse('User not found', 404);
                return;
            }

            $this->userService->deleteUser($userId);

            $this->successResponse(null, 200, 'User deleted successfully');
        } catch (\RuntimeException $e) {
            $this->authExceptionResponse($e);
        } catch (\Exception $e) {
            $this->errorResponse('Failed to delete user: ' . $e->getMessage(), 500);
        }
    }
}
