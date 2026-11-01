# PitStop OS

**Your vehicle. Our priority.**

Automotive Service & Customer Support Platform for a university Web Programming course. PitStop OS connects vehicle owners with workshop staff, replacing scattered repair updates, vehicle records and support conversations with one workspace.

## Current scope

Milestone 1: responsive static single-page application with 21 HTML view partials, related in-memory sample data, functional demonstration interactions and a draft ERD. Records are sample data; the main user name follows the student's request. No backend, database, real authentication, external vehicle/weather API, email or booking service is connected. Refreshing resets demo changes and selects the customer role.

M1 is being completed as the foundation for M2, despite the missed grading deadline, as clarified by the student. Local work is on `milestone1`; no PR, collaborator invitation or merge has been performed.

## Run locally

From the repository root, with Python 3 installed:

```powershell
cd C:\Users\User\Web-Programming
py -m http.server 8000 --bind 127.0.0.1 --directory frontend
```

Open **http://127.0.0.1:8000/**. Stop the server with Ctrl+C. If `py` is unavailable, use `python` instead. This computer also has the bundled runtime:

```powershell
& "C:\Users\User\.cache\codex-runtimes\codex-primary-runtime\dependencies\python\python.exe" -m http.server 8000 --bind 127.0.0.1 --directory frontend
```

Alternatively, with PHP installed: `php -S 127.0.0.1:8000 -t frontend`. This serves static files only; it does not implement a PHP backend. XAMPP can serve the frontend directory as well.

Do not double-click index.html: ES modules and fetched HTML partials require HTTP. There is no build step or npm install. Bootstrap CSS and automotive artwork are stored locally. Google Fonts are an optional visual enhancement; system sans-serif fallbacks keep the app usable without internet.

## Demo roles and features

Use **Explore the platform** on the home page, the workshop/mechanic links, or the **VIEW AS** selector in the workspace. The header theme button switches light/dark mode and remembers the choice locally.

| Role | Seed identity | Experience |
| --- | --- | --- |
| Customer | Amina Brković, amina@example.test | Dashboard, vehicle add/edit/delete, VIN preview, service requests, timelines, history, support and profile |
| Admin | Nina Marić, nina@example.test | Customer and vehicle management, approvals, mechanic assignment, work orders, parts, services and ticket inbox |
| Mechanic | Emin Bešić, emin@example.test | Assigned-job dashboard, workshop orders, diagnosis/status updates and support |

These are simulated identities, not test credentials. Login accepts a syntactically valid sample email and a sample password of at least eight characters; it opens the seeded customer demonstration without authenticating. Registration checks matching passwords and opens the same demo; it does not create or store credentials. Do not enter real passwords.

- One responsive SPA with browser back/forward hash navigation, reusable sidebar/topbar, tables, cards, badges, forms, modal dialogs, empty states and toasts.
- Search and status/urgency filters operate on local data.
- Vehicle VIN preview recognizes only `1HGCM82633A004352`. Unknown VINs keep manual input available.
- Requests are initially Requested; admin approval creates an order and service item. Later status changes synchronize the request and order. Completion computes final cost from item price snapshots.
- Referenced records cannot be deleted. Changing ownership of a vehicle with history is blocked in the demo.
- Inventory highlights quantities at or below the minimum. Customer, part and service create/edit/delete workflows update in-memory state.
- Ticket creation and replies stay within the demo; nothing is sent externally.
- Dashboard metrics and pipeline/revenue summaries derive from the same data used in the lists.
- The landing page uses generated performance-car artwork with a black/red identity. Vehicle record screens retain neutral placeholders. The image is promotional artwork, not a customer vehicle photograph; see docs/hero-image-prompt.md.

## Required folder structure

```text
frontend/
  index.html
  css/                  styles.css, components.css, responsive.css, themes.css
  js/                   app.js, router.js, pages.js, mockData.js
    components/         ui.js
    services/           store.js (mock data access)
  views/                one HTML partial per screen
  assets/
    images/             hero-performance.png, vehicle.svg
    icons/
    vendor/             bootstrap.min.css
backend/
  routes/
  services/
  dao/
  middleware/
  config/
  logs/
database/               erd.md
docs/                   AI_LOG.md, MILESTONE1_CHECKLIST.md
.github/agents/         frontend, backend, reviewer instructions
.agents/skills/         update-ai-log/SKILL.md
tests/                  data.test.js
AGENTS.md
.gitignore
```

The backend directories contain `.gitkeep` only so Git preserves the structure. No PHP is embedded in frontend files. Logs, real configuration and dependencies are ignored.

## Technology and architecture

Current: HTML, CSS, native JavaScript ES modules, Bootstrap 5.3.3 CSS, native dialog and in-memory mock state. No React/Vue or frontend bundler.

`index.html -> router.js -> views/*.html -> pages.js/shared UI -> js/services/store.js -> mockData.js`

Views are HTML partials loaded with fetch, not independent documents. Page-specific mock content is rendered into their named content region by pages.js. Event handling is delegated in app.js. The single data service owns mutations and referential safeguards. HTML escaping is applied to user-controlled display values.

Planned backend: **PHP + FlightPHP REST + PDO + MySQL**, JWT role/ownership authorization, reusable request/error middleware and logging, OpenAPI/Swagger. Request flow: frontend service -> AJAX -> route -> middleware/service -> DAO -> database. Only services call DAO classes. Prepared statements are mandatory.

For M4, replace the mock service with API-backed functions and await responses in controllers. The current service interface is synchronous; API adoption will require asynchronous handling. UI demo route filtering is not a security boundary.

## Database plan

See [database/erd.md](database/erd.md) for the renderable Mermaid ERD, keys, cardinalities and constraints.

Nine entities: users, vehicles, service_requests, services, service_orders, parts, service_order_items, support_tickets and ticket_messages. Customers own vehicles and submit requests. A request can create one order, assigned to a mechanic. Each order contains labor/part items with price snapshots. Tickets belong to customers, optionally reference requests and contain sender-attributed messages.

Refinements: service_requests references the service catalog through service_id; vehicles includes transmission; users includes city; parts includes category. These support the requested screens. Exactly one of service_id or part_id must be set on an order item. Preserve historical data when deleting or archiving.

## Planned external integrations

1. **NHTSA vPIC:** VIN decoding in the vehicle form; manual fallback when missing/incomplete. M1 uses a labeled fixed sample only.
2. **Open-Meteo:** weather context for an upcoming appointment near the customer's city. M1 shows labeled sample weather; never vehicle diagnostics or a real forecast. Real integration must respect the provider's forecast horizon.

In M3, use backend services with bounded timeouts, error handling and logging; return a useful fallback if a provider is unavailable. No live external API calls occur in M1.

## Milestones

| Milestone | Scope |
| --- | --- |
| M1 | Static SPA, structure, draft ERD and documentation (this branch) |
| M2 | MySQL schema, sample-data SQL export, reusable BaseDao and entity DAO CRUD using PDO |
| M3 | Business services, FlightPHP routes, OpenAPI/Swagger and external API endpoint |
| M4 | JWT, authorization, middleware/logging, fully connected frontend |
| M5 | Final frontend service/MVC separation, validation and public deployment |

Do not merge milestone branches yourself. Course workflow: PR from milestone1 into main, collaborator/reviewer `ibu-web-programming2001`, reviewer performs the merge. The student should coordinate the late-M1/M2 branch workflow with the instructor; do not silently bypass the course merge rule.

## Validation

With Node.js 20+ available, run `node --test tests/data.test.js` (or `npm test`). Tests need no npm packages. See [the checklist](docs/MILESTONE1_CHECKLIST.md) for browser checks, scope coverage and remaining review.

## Future links and configuration

- Swagger URL: **not implemented (M3)**.
- Deployed application: **not deployed (M5)**.
- Real test accounts: **not created (M4/M5)**.
- Database import: **no SQL file yet (M2)**.
- Secret configuration: **not needed in M1**; M2 adds ignored backend/config/config.php and a safe config.example.php.

Bootstrap is distributed under MIT; copyright/license attribution is retained in its CSS header. The optional fonts are served by Google Fonts. The application was developed with AI assistance according to the student's requirements; see [AI_LOG.md](docs/AI_LOG.md).
