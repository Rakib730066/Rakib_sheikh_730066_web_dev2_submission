<?php

namespace App\Controllers;

use App\Framework\Controller;
use App\Services\AuthService;
use App\Services\IAuthService;
use App\Services\IUserService;
use App\Services\UserService;
use App\Helpers\JsonResponse;
use App\Middleware\AuthMiddleware;

class AuthController extends Controller
{
    private IAuthService $authService;
    private IUserService $userService;
    private AuthMiddleware $authMiddleware;

    public function __construct()
    {
        $this->authService = new AuthService();
        $this->userService = new UserService();
        $this->authMiddleware = new AuthMiddleware();
    }

    public function register(): void
    {
        try {
            $data = $this->getJsonInput();
            $result = $this->authService->register($data);
            JsonResponse::success($result, 201, 'User registered successfully');
        } catch (\InvalidArgumentException $e) {
            JsonResponse::error($e->getMessage(), 422);
        } catch (\RuntimeException $e) {
            JsonResponse::error($e->getMessage(), 400);
        } catch (\Exception $e) {
            JsonResponse::error('An error occurred during registration', 500);
        }
    }

    public function login(): void
    {
        try {
            $data = $this->getJsonInput();
            $result = $this->authService->login($data);
            JsonResponse::success($result, 200, 'Login successful');
        } catch (\InvalidArgumentException $e) {
            JsonResponse::error($e->getMessage(), 422);
        } catch (\RuntimeException $e) {
            JsonResponse::error($e->getMessage(), 401);
        } catch (\Exception $e) {
            JsonResponse::error('An error occurred during login', 500);
        }
    }

    public function me(): void
    {
        try {
            $userData = $this->authMiddleware->getAuthenticatedUser();
            if (!$userData) {
                JsonResponse::error('Unauthorized', 401);
                return;
            }

            $user = $this->userService->getUser((int)$userData['id']);
            if (!$user) {
                JsonResponse::error('Unauthorized', 401);
                return;
            }

            JsonResponse::success(['user' => $user], 200);
        } catch (\Exception $e) {
            JsonResponse::error('Unauthorized', 401);
        }
    }

    public function validate(): void
    {
        $this->me();
    }
}
