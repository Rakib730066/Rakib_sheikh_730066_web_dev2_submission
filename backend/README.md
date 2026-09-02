# Backend - Event Management API

This is the PHP REST API for the project. It handles users, events, registrations
and JWT authentication.

## Two Docker Compose files (read this first)

There are two Compose files:

- `../docker-compose.yml` in the project root
- `backend/docker-compose.yml` in this folder

Don't run both at once. They both use ports 80 and 3306 so they'll conflict.

The root Compose file starts the full Dockerized app, including the Vue frontend on
`http://localhost:5173`. The backend Compose file is backend-only and includes
PhpMyAdmin. `http://localhost` is the API, not the frontend page.

Run from the project root (recommended for the full app):

```
cd ..
docker compose up -d --build
```

This starts:

- Nginx on http://localhost
- PHP-FPM
- MySQL on port 3306
- Vue frontend on http://localhost:5173

Run from this backend folder (this one also includes PhpMyAdmin):

```
docker compose up -d --build
```

This starts:

- Nginx on http://localhost
- PHP-FPM
- MySQL on port 3306
- PhpMyAdmin on http://localhost:8080

PhpMyAdmin only comes with this backend Compose file, not the root one.

## Database

```
Host:     mysql
Database: developmentdb
Username: developer
Password: secret123
```

## Test accounts

```
Admin
  email:    admin@example.com
  password: admin123

User
  email:    john@example.com
  password: password123
```

## API routes

Everything is under `/api`. Protected routes need a token in the header:

```
Authorization: Bearer <token>
```

Auth:

```
POST /api/auth/register
POST /api/auth/login
GET  /api/auth/me
POST /api/auth/validate
```

Users:

```
GET    /api/users/profile
PUT    /api/users/profile
GET    /api/users
GET    /api/users/{userId}
DELETE /api/users/{userId}
```

Events:

```
GET    /api/events
GET    /api/events/{id}
POST   /api/events
PUT    /api/events/{id}
DELETE /api/events/{id}
GET    /api/events/{id}/registrations
```

Registrations:

```
POST   /api/registrations
GET    /api/registrations/user/{userId}
GET    /api/registrations/event/{eventId}
DELETE /api/registrations/{id}
```

## Quick tests

Health check:

```
curl http://localhost/api/health
```

List events:

```
curl http://localhost/api/events
```

Login:

```
curl -X POST http://localhost/api/auth/login ^
  -H "Content-Type: application/json" ^
  -d "{\"email\":\"admin@example.com\",\"password\":\"admin123\"}"
```

## Troubleshooting

If you get a port conflict (80, 3306 or 8080), you probably have both Compose stacks
running. Check with:

```
netstat -ano | findstr ":80 :3306 :8080"
```

Then stop the one you started (run this from the same folder you started it in):

```
docker compose down
```

On Windows, if `npm` is blocked by PowerShell, use `npm.cmd` instead.
