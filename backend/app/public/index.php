<?php

// CORS for local development
$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
if (preg_match('/^https?:\/\/(localhost|127\.0\.0\.1|::1)(:\d+)?$/', $origin)) {
    header('Access-Control-Allow-Origin: ' . $origin);
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
    header('Access-Control-Allow-Credentials: true');
    header('Access-Control-Max-Age: 86400');
}

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require __DIR__ . '/../vendor/autoload.php';

use FastRoute\RouteCollector;
use App\Helpers\JsonResponse;
use function FastRoute\simpleDispatcher;

$dispatcher = simpleDispatcher(function (RouteCollector $r) {
    $r->addRoute('GET', '/health', ['App\Controllers\HealthController', 'check']);
    $r->addRoute('GET', '/health/ready', ['App\Controllers\HealthController', 'ready']);
    $r->addRoute('GET', '/stats', ['App\Controllers\StatsController', 'summary']);
    
    $r->addRoute('POST', '/auth/register', ['App\Controllers\AuthController', 'register']);
    $r->addRoute('POST', '/auth/login', ['App\Controllers\AuthController', 'login']);
    $r->addRoute('GET', '/auth/me', ['App\Controllers\AuthController', 'me']);
    $r->addRoute('POST', '/auth/validate', ['App\Controllers\AuthController', 'validate']);

    $r->addRoute('GET', '/users/profile', ['App\Controllers\UserController', 'getProfile']);
    $r->addRoute('PUT', '/users/profile', ['App\Controllers\UserController', 'updateProfile']);
    $r->addRoute('GET', '/users', ['App\Controllers\UserController', 'getAllUsers']);
    $r->addRoute('GET', '/users/{userId}', ['App\Controllers\UserController', 'getUser']);
    $r->addRoute('DELETE', '/users/{userId}', ['App\Controllers\UserController', 'deleteUser']);

    $r->addRoute('GET', '/events', ['App\Controllers\EventController', 'getAll']);
    $r->addRoute('GET', '/events/{id}', ['App\Controllers\EventController', 'get']);
    $r->addRoute('POST', '/events', ['App\Controllers\EventController', 'create']);
    $r->addRoute('PUT', '/events/{id}', ['App\Controllers\EventController', 'update']);
    $r->addRoute('DELETE', '/events/{id}', ['App\Controllers\EventController', 'delete']);
    $r->addRoute('GET', '/events/{id}/registrations', ['App\Controllers\EventController', 'getRegistrations']);

    $r->addRoute('POST', '/registrations', ['App\Controllers\RegistrationController', 'create']);
    $r->addRoute('GET', '/registrations/user/{userId}', ['App\Controllers\RegistrationController', 'getUserRegistrations']);
    $r->addRoute('DELETE', '/registrations/{id}', ['App\Controllers\RegistrationController', 'delete']);
    $r->addRoute('GET', '/registrations/event/{eventId}', ['App\Controllers\RegistrationController', 'getByEvent']);
});

$httpMethod = $_SERVER['REQUEST_METHOD'];
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$basePath = '/api';
$uri = str_replace($basePath, '', $uri);

$routeInfo = $dispatcher->dispatch($httpMethod, $uri);
switch ($routeInfo[0]) {
    case FastRoute\Dispatcher::NOT_FOUND:
        JsonResponse::error('Endpoint not found', 404);

    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        $allowedMethods = $routeInfo[1];
        header('Allow: ' . implode(', ', $allowedMethods));
        JsonResponse::error('Method not allowed', 405, ['allowed_methods' => $allowedMethods]);

    case FastRoute\Dispatcher::FOUND:
        $handler = $routeInfo[1];
        $vars = $routeInfo[2];

        try {
            $class = $handler[0];
            $method = $handler[1];
            $controller = new $class();
            $controller->$method($vars);

        } catch (\Exception $e) {
            http_response_code(500);
            header('Content-Type: application/json');
            $response = ['error' => 'Internal Server Error'];
            if (getenv('APP_DEBUG') === 'true') {
                $response['message'] = $e->getMessage();
            }
            echo json_encode($response);
        }
        break;
}
