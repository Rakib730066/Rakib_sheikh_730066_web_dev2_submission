# Smart Event Management System

This is my final project for Web Development 2. It is an event management app with a
Vue 3 frontend and a PHP REST API backend. Users can register, log in, browse events,
and sign up for them. Admins can create and manage events.

## How to run it

From the project root, start the full app with Docker:

```bash
docker compose up -d --build
```

This starts the frontend, backend API, PHP service, and MySQL database.

Open these URLs:

```text
Frontend Vue app: http://localhost:5173
Backend API:      http://localhost/api
Backend health:   http://localhost/api/health
Backend ready:    http://localhost/api/health/ready
Events API:       http://localhost/api/events
```

Important: `http://localhost` on its own is the backend API through Nginx, not the
frontend. If you open it in the browser, it returns an `Endpoint not found` API response.
That is expected because the API routes are under `/api`.

## Database

```text
Host:     mysql
Database: developmentdb
Username: developer
Password: secret123
```

The database schema and seed data are in `backend/database/schema.sql`. The MySQL
container loads this file automatically the first time the Docker volume is created.

## Test accounts

```text
Admin
  email:    admin@example.com
  password: admin123

User
  email:    john@example.com
  password: password123
```

## PhpMyAdmin

PhpMyAdmin is not part of the root Docker setup. If you want PhpMyAdmin, run the
separate backend Compose file instead:

```bash
cd backend
docker compose up -d --build
```

Then open:

```text
http://localhost:8080
```

Do not run the root and backend Compose files at the same time. They both use ports
`80` and `3306`, so they will conflict.

## Project structure

```text
backend/    PHP REST API (users, events, registrations, JWT auth)
frontend/   Vue 3 app (Vite, Pinia, Vue Router, Axios, Tailwind)
```

There is a separate README in each folder with more detail.
