# PitStop OS

Automotive service and customer support platform for vehicle owners, mechanics and workshop administrators.

## Current milestone

Milestone 1 includes the static frontend, project structure and database ERD. The frontend is a responsive single-page application with light and dark themes. It currently uses in-memory sample data; API integration and real authentication follow in later milestones.

## Technologies

- Frontend: HTML, CSS, JavaScript and Bootstrap 5.
- Planned backend: PHP, PDO, MySQL, FlightPHP REST API, JWT and OpenAPI/Swagger.

## Run with XAMPP

1. Place the project folder in XAMPP's `htdocs` directory, for example `htdocs/Web-Programming`.
2. Start Apache and MySQL in the XAMPP Control Panel.
3. Open `http://localhost/Web-Programming/frontend/`.
No npm install or build step is required. Frontend changes reset on page reload until backend integration is implemented.

## Roles and sample users

| Role | Name | Email | Features |
| --- | --- | --- | --- |
| Customer | Amina Brković | amina@example.test | Vehicles, service requests, history and support |
| Admin | Nina Marić | nina@example.test | Customers, vehicles, requests, orders, services, parts and support |
| Mechanic | Emin Bešić | emin@example.test | Assigned work orders, diagnosis and status updates |

Use **Explore the platform** and the **VIEW AS** selector to preview roles. Frontend login is simulated.

## Structure

```text
frontend/       css/, js/, views/, assets/, index.html
backend/        routes/, services/, dao/, middleware/, config/, logs/
database/       erd.md
docs/           AI_LOG.md
.github/agents/ frontend, backend and reviewer instructions
.agents/skills/ update-ai-log/SKILL.md
tests/          data.test.js
AGENTS.md
```

## Database plan

The nine entities are `users`, `vehicles`, `services`, `parts`, `service_requests`, `service_orders`, `service_order_items`, `support_tickets` and `ticket_messages`. See the [ERD](database/erd.md) for fields and relationships.

Customers own vehicles and submit service requests. Each request can produce one work order assigned to a mechanic. Order items contain services or parts and preserve their unit prices. Support tickets belong to customers, optionally link to a service request and contain messages.

The SQL database and DAO implementation are planned for Milestone 2.

Future request flow: route → service → DAO → database. Services will enforce ownership, roles and status transitions. Routes, JWT, logging and frontend API integration are scheduled for later milestones.

## Tests

From the project root, with Node.js available in PATH:

```sh
node --test tests/data.test.js
```

Frontend tests cover routes, sample-data relationships and escaping.

## Planned external APIs

- **NHTSA vPIC:** VIN decoding in the vehicle form, with manual entry as a fallback.
- **Open-Meteo:** weather information for upcoming service appointments.

Both currently use sample results. Live integrations are planned for M3/M4.

## Links and documentation

- Swagger: planned for M3.
- Deployed application: planned for M5.
- [AI usage log](docs/AI_LOG.md)
- [Project instructions](AGENTS.md)

Bootstrap's license is included in its CSS header. Landing-page artwork was generated with AI; its prompt is recorded in [hero-image-prompt.md](docs/hero-image-prompt.md).
