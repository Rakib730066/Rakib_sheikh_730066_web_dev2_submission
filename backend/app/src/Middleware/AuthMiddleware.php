<?php

namespace App\Middleware;

use App\Services\JwtService;

class AuthMiddleware
{
    private JwtService $jwtService;

    public function __construct()
    {
        $this->jwtService = new JwtService();
    }

    public function authenticate(): array
    {
        try {
            $token = JwtService::extractTokenFromHeader();

            if (!$token) {
                throw new \RuntimeException('No authentication token provided');
            }

            $userData = $this->jwtService->decodeToken($token);

            if (!$userData) {
                throw new \RuntimeException('Invalid or expired token');
            }

            return $userData;

        } catch (\RuntimeException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw new \RuntimeException('Authentication failed');
        }
    }

    public function requireAdmin(): array
    {
        $userData = $this->authenticate();

        if (!isset($userData['role']) || $userData['role'] !== 'admin') {
            throw new \RuntimeException('Admin role required');
        }

        return $userData;
    }

    public function getAuthenticatedUser(): ?array
    {
        try {
            return $this->authenticate();
        } catch (\RuntimeException $e) {
            return null;
        }
    }

}
