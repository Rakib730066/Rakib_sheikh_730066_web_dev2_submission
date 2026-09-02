<?php

namespace App\Framework;

use App\Helpers\JsonResponse;

class Controller
{
    protected function getJsonInput(): array
    {
        try {
            $input = file_get_contents('php://input');
            return json_decode($input, true) ?? [];
        } catch (\Exception $e) {
            return [];
        }
    }

    protected function successResponse(mixed $data = null, int $statusCode = 200, ?string $message = null): void
    {
        JsonResponse::success($data, $statusCode, $message);
    }

    protected function errorResponse(string $message, int $statusCode = 400, mixed $errors = null): void
    {
        JsonResponse::error($message, $statusCode, $errors);
    }

    protected function paginatedResponse(array $data, int $total, int $page, int $limit, int $statusCode = 200): void
    {
        JsonResponse::paginated($data, $total, $page, $limit, $statusCode);
    }

    protected function authExceptionResponse(\RuntimeException $e): void
    {
        if ($e->getMessage() === 'Admin role required' || $e->getMessage() === 'Required role not found') {
            $this->errorResponse('Forbidden: Admin access required', 403);
            return;
        }

        $this->errorResponse('Unauthorized: Missing or invalid token', 401);
    }

    protected function mapJsonToClass(string $className): object
    {
        $data = $this->getJsonInput();
        $instance = new $className();

        foreach ($data as $key => $value) {
            if (property_exists($instance, $key)) {
                $instance->$key = $value;
            }
        }

        return $instance;
    }
}
