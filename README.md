# PitStop OS

Automotive service and customer support platform for vehicle owners, mechanics and workshop administrators.

## Current milestone

Milestone 2 includes a MySQL database with sample data and a PHP DAO layer. The frontend is a responsive single-page application with light and dark themes. It currently uses in-memory sample data; API integration and real authentication follow in later milestones.

## Technologies

- Frontend: HTML, CSS, JavaScript and Bootstrap 5.
- Backend: PHP 8.2+, PDO and MySQL 8.0.16+ or XAMPP MariaDB 10.4+.
- Planned: FlightPHP REST API, JWT and OpenAPI/Swagger.

## Run with XAMPP

1. Place the project folder in XAMPP's `htdocs` directory, for example `htdocs/Web-Programming`.
2. Start Apache and MySQL in the XAMPP Control Panel.
3. Open `http://localhost/Web-Programming/frontend/`.
4. Open phpMyAdmin using the MySQL **Admin** button. Import [database/pitstop_os.sql](database/pitstop_os.sql). The file creates `pitstop_os`; import it only when that database has no existing tables.
5. Copy `backend/config/config.example.php` to `backend/config/config.php` once. Set the MySQL host, port, database name, username and password in the local copy. Keep an existing configured file.

The default database name is `pitstop_os`, host is `127.0.0.1` and port is `3306`. Use the port and credentials configured in your XAMPP installation. PHP must have `pdo_mysql` enabled. The real config file is excluded from Git.

No npm install or build step is required. Frontend changes reset on page reload until backend integration is implemented.

## Roles and sample users

| Role | Name | Email | Features |
| --- | --- | --- | --- |
| Customer | Amina Brković | amina@example.test | Vehicles, service requests, history and support |
| Admin | Nina Marić | nina@example.test | Customers, vehicles, requests, orders, services, parts and support |
| Mechanic | Emin Bešić | emin@example.test | Assigned work orders, diagnosis and status updates |

Use **Explore the platform** and the **VIEW AS** selector to preview roles. Frontend login is simulated. The SQL sample users have bcrypt hashes for the sample password `PitStop-M2-Sample!`; these are not yet connected to frontend login.

## Structure

```text
frontend/       css/, js/, views/, assets/, index.html
backend/        routes/, services/, dao/, middleware/, config/, logs/
database/       pitstop_os.sql, erd.md
docs/           AI_LOG.md
.github/agents/ frontend, backend and reviewer instructions
.agents/skills/ update-ai-log/SKILL.md
tests/          data.test.js, dao.test.php
AGENTS.md
```

## Database and DAO

The nine entities are `users`, `vehicles`, `services`, `parts`, `service_requests`, `service_orders`, `service_order_items`, `support_tickets` and `ticket_messages`. See the [ERD](database/erd.md) for fields and relationships.

Customers own vehicles and submit service requests. Each request can produce one work order assigned to a mechanic. Order items contain services or parts and preserve their unit prices. Support tickets belong to customers, optionally link to a service request and contain messages.

Each entity has a DAO extending `BaseDao`, with `getAll()`, `getById()`, `countAll()`, `insert()`, `update()` and `delete()`. The shared PDO connection uses native prepared statements and utf8mb4. Writable columns are whitelisted; ordinary user reads exclude password hashes. Transaction helpers support grouped changes.

Future request flow: route → service → DAO → database. Services will enforce ownership, roles and status transitions. Routes, JWT, logging and frontend API integration are scheduled for later milestones.

## Tests

From the project root, with PHP and Node.js available in PATH:

```sh
php tests/dao.test.php
node --test tests/data.test.js
```

Alternatively, use the full path to `php.exe` inside your XAMPP `php` folder. Run DAO tests against an imported sample database with `config.php` configured. They cover CRUD for all nine entities, SQL injection handling, constraints, JOINs and rollback. Test records are rolled back; auto-increment counters may advance.

The DAO tests passed on MySQL 8.0.39 and XAMPP MariaDB 10.4.32. Frontend tests cover routes, sample-data relationships and escaping.

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
