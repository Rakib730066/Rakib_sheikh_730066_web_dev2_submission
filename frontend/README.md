# Frontend - Event Management App

This is the Vue 3 frontend for the project, built with Vite. You can register, log in,
browse events and sign up for them. Admins can also create and manage events.

## Run with Docker

The normal submission flow starts the frontend together with the backend from the
project root:

```bash
docker compose up -d --build
```

The Dockerized frontend is served at:

```text
http://localhost:5173
```

It calls the backend API at:

```text
http://localhost/api
```

## Local development

You can still run the Vite dev server locally while the backend Docker stack is
running. From the project root, start the backend/API first:

```bash
docker compose up -d --build
```

Then in a second terminal:

```bash
cd frontend
npm install
npm.cmd run dev
```

The local dev server runs at `http://localhost:5173`. If that port is taken, Vite
picks the next one, such as `5174` or `5175`, so check the terminal output.

## Tech stack

- Vue 3 with the Composition API (`<script setup>`)
- Vite
- Vue Router
- Pinia for state
- Tailwind CSS
- Axios for API calls

## Folder structure

```text
src/
  components/
    atoms/        basic UI (Button, Input, Badge, Alert...)
    molecules/    compound pieces (LoginForm, SearchBar...)
    organisms/    page sections (Header, Navbar, Footer, EventCard)
    pages/        full pages (LoginPage, EventsPage...)
  stores/         Pinia stores (auth, events)
  utils/
    axiosConfig.js          axios instance + base URL + interceptors
    eventService.js         event API calls
    registrationService.js  registration API calls
  App.vue
  main.js
```

## How the API calls work

The Axios setup lives in `src/utils/axiosConfig.js`. That file configures the base URL
and the auth interceptor. The actual API calls are split into service files like
`eventService.js` and `registrationService.js`, and the auth logic
(login/register/logout) is in `src/stores/auth.js`.

The base URL is read from an environment variable:

```text
import.meta.env.VITE_API_URL || ''
```

For Docker builds, root `docker-compose.yml` sets `VITE_API_URL` to
`http://localhost`, and the frontend code calls `/api/...` paths from there. For local
Vite development, the empty default uses the Vite proxy for `/api` requests.

## Authentication

When you log in, a JWT token gets saved in localStorage and sent in the
`Authorization` header on every request. When you log out, the token is removed. All
of that is handled in `src/stores/auth.js`.

## npm scripts

```text
npm run dev       start the dev server
npm run build     build for production
npm run preview   preview the production build
```

On Windows PowerShell, use `npm.cmd`, for example:

```bash
npm.cmd run dev
npm.cmd run build
```

## Troubleshooting

Port 5173 already in use? Vite will move to the next free port, so check the terminal.

Can't reach the API? Make sure the backend Docker stack is running and
`http://localhost/api/health` responds.

## Notes

- Password validation is done on the backend.
- Event images are stored as URLs, not uploaded files.
- The user role (admin or user) decides what they can do. The frontend hides the admin
  buttons based on role, but the backend checks it too.
