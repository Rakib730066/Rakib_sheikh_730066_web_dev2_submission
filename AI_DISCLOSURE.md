# AI Disclosure Statement

## AI tools used
- I used AI assistance during this project as a support tool. The AI was used to help me understand problems, review parts of my code, debug errors, and improve documentation. The project idea, feature choices, final decisions, testing, and submission were done by me.

## What AI was used for
- Explaining errors and helping me understand how to fix them.
- Reviewing parts of the PHP REST API and Vue frontend structure.
- Writing and improving README files so the setup steps, credentials, API URLs, and Docker instructions match the actual project.
- Helping identify unused files and small cleanup items.

## What I did myself
- Chose the application use case, core features, and overall project direction.
- Made the main architecture decisions for the event management system, including the backend API, database schema, frontend pages, and user/admin workflows.
- Integrated the frontend, backend, Docker services, database seed data, and authentication flow into one working application.
- Manually tested the app using the provided admin and user accounts and checked that the final behavior matches the project requirements.

## Verification of understanding
I have reviewed the codebase and understand how the main parts work together: API requests enter through the PHP router, controllers delegate business logic to services, services use repositories for database access, and PDO prepared statements are used for safe SQL queries. I also understand the JWT authentication flow, including token creation, protected routes, and admin role checks.

I have also reviewed the Vue frontend structure, including the component hierarchy, Vue Router pages, Pinia authentication store, Axios configuration, and how frontend API calls connect to the backend. I can explain the project structure, trace a feature from the browser to the database, and make changes to the application without relying on AI.
