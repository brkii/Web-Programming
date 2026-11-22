# PitStop OS

Automotive service and customer support platform for vehicle owners, mechanics and workshop administrators.

## Current milestone

Milestone 3 adds services, FlightPHP CRUD routes for all nine entities, OpenAPI/Swagger and live VIN decoding. The responsive frontend retains light/dark themes and in-memory sample data. JWT authentication and frontend API integration follow in M4.

## Technologies

- Frontend: HTML, CSS, JavaScript and Bootstrap 5.
- Backend: PHP 8.2+, PDO and MySQL 8.0.16+ or XAMPP MariaDB 10.4+.
- API: FlightPHP 3, swagger-php 6 and NHTSA vPIC. JWT follows in M4.

## Run with XAMPP

1. Place the project folder in XAMPP's `htdocs` directory, for example `htdocs/Web-Programming`.
2. Start Apache and MySQL in the XAMPP Control Panel.
3. Open `http://localhost/Web-Programming/frontend/`.
4. Open phpMyAdmin using the MySQL **Admin** button. Import [database/pitstop_os.sql](database/pitstop_os.sql). The file creates `pitstop_os`; import it only when that database has no existing tables.
5. Copy `backend/config/config.example.php` to `backend/config/config.php` once. Set the MySQL host, port, database name, username and password in the local copy. Keep an existing configured file.
6. Run `composer install --working-dir=backend` from the project root. PHP 8.2+ needs `pdo_mysql`, `curl` and `mbstring` enabled.
7. Open the [API](http://localhost/Web-Programming/backend/) and [Swagger UI](http://localhost/Web-Programming/backend/public/v1/docs/). Adjust `Web-Programming` if your folder has a different name. Apache needs `mod_rewrite` and `AllowOverride All` for the included `.htaccess`.

The default database name is `pitstop_os`, host is `127.0.0.1` and port is `3306`. Use the port and credentials configured in your XAMPP installation. PHP must have `pdo_mysql` enabled. The real config file is excluded from Git.

No npm install or frontend build step is required. Keep the existing M2 database; M3 does not change its schema. Frontend changes still reset on reload until M4.

Alternatively, leave MySQL running and start PHP from the project root:

```sh
php -S localhost:8000 server.php
```

Then open `http://localhost:8000/frontend/` and [Swagger UI](http://localhost:8000/backend/public/v1/docs/). Swagger's **Try it out** sends real requests to the configured database. The API is for local development until M4 adds authentication.

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
backend/        routes/, services/, dao/, utils/, middleware/, config/, logs/
                public/v1/docs/ (Swagger UI, schemas and paths)
database/       pitstop_os.sql, erd.md
docs/           AI_LOG.md
.github/agents/ frontend, backend and reviewer instructions
.agents/skills/ update-ai-log/SKILL.md
tests/          frontend, DAO, service, HTTP and VIN tests
server.php      local PHP router
AGENTS.md
```

## Database and API

The nine entities are `users`, `vehicles`, `services`, `parts`, `service_requests`, `service_orders`, `service_order_items`, `support_tickets` and `ticket_messages`. See the [ERD](database/erd.md) for fields and relationships.

Customers own vehicles and submit service requests. Each request can produce one work order assigned to a mechanic. Order items contain services or parts and preserve their unit prices. Support tickets belong to customers, optionally link to a service request and contain messages.

Each entity has a DAO extending `BaseDao`, with `getAll()`, `getById()`, `countAll()`, `insert()`, `update()` and `delete()`. The shared PDO connection uses native prepared statements and utf8mb4. Writable columns are whitelisted; ordinary user reads exclude password hashes. Transaction helpers support grouped changes.

Request flow: route → service → DAO → database. Each entity has its own service and routes file. Every resource supports `GET /resource`, `POST /resource`, and `GET`, `PUT`, `DELETE /resource/{id}`. PUT accepts partial updates; omitted fields are preserved. Resources: `users`, `vehicles`, `services`, `parts`, `service-requests`, `service-orders`, `service-order-items`, `support-tickets`, `ticket-messages`.

Services validate customer ownership, mechanic assignments and status transitions. Order creation includes the primary service item. Parts reserve available stock; removing items or cancelling work returns it. Completion calculates `final_cost` from unchanged price snapshots and synchronizes the request status. Tickets start with an opening message. Referenced records and processed history are protected from deletion.

Successful reads/updates/deletes return 200, creation returns 201, invalid data/rules return 400, missing records return 404 and unexpected errors return 500. Errors use `{"error":"..."}` without SQL or stack traces. The shared handler supports 401/403 exceptions; JWT checks that produce those responses follow in M4. Relationship validation does not authenticate the caller.

## Tests

From the project root, with PHP and Node.js available in PATH:

```sh
php tests/dao.test.php
php tests/services.test.php
php tests/vin.test.php
node --test tests/data.test.js
php tests/api.test.php http://localhost:8000/backend
```

Use the full path to XAMPP's `php.exe` if PHP is not in PATH. Run database tests against a separate imported sample database. `DB_HOST`, `DB_PORT`, `DB_NAME`, `DB_USER` and `DB_PASSWORD` environment variables override the example configuration. DAO/service tests roll back fixtures. HTTP tests require a running server configured for that test database and delete only records they create; auto-increment counters may advance.

Tests cover CRUD, prepared statements, constraints, price snapshots, stock rollback, status changes, HTTP responses, Swagger references and external API failures. VIN unit tests use controlled responses; the live integration can be tried in Swagger.

## External API

`GET /backend/vin/{vin}?model_year=2003` calls [NHTSA vPIC](https://vpic.nhtsa.dot.gov/api/) and returns vehicle details for the VIN form planned for M4. Example VIN: `1HGCM82633A004352`. No API key is required. Connections time out after 3 seconds and requests after 10 seconds. Partial decoding returns `complete: false`; failures are logged to ignored `backend/logs/error.log` and return 503 with a manual-entry fallback message. The frontend VIN/weather previews remain samples until M4; Open-Meteo is not yet implemented.

## Links and documentation

- [Swagger UI (XAMPP)](http://localhost/Web-Programming/backend/public/v1/docs/)
- [OpenAPI JSON (XAMPP)](http://localhost/Web-Programming/backend/public/v1/docs/swagger.php)
- Deployed application: planned for M5.
- [AI usage log](docs/AI_LOG.md)
- [Project instructions](AGENTS.md)

Bootstrap's license is included in its CSS header. Landing-page artwork was generated with AI; its prompt is recorded in [hero-image-prompt.md](docs/hero-image-prompt.md).
