# AGENTS.md

## Project
PitStop OS: automotive service and customer support SPA.
Roles: customer, admin, mechanic. Current scope: Milestone 3 (services, REST API, Swagger and external API).

## Technology stack
- Frontend: HTML, CSS, JavaScript, Bootstrap.
- Backend: PHP 8.2+, FlightPHP, PDO and MySQL; swagger-php uses PHP 8 attributes.

## Folder structure
- frontend/: css/, js/, views/, assets/ and index.html.
- backend/: routes/, services/, dao/, utils/, middleware/, config/, logs/, public/v1/docs/.
- database/: ERD and SQL export with sample data.
- docs/: project documentation and AI_LOG.md.

## Rules
- Each screen has its own HTML view; shared CSS/JS load once.
- Keep frontend and backend separate. Support mobile, light and dark themes.
- Backend flow: route -> service -> DAO -> database. Only services call DAO.
- Use PDO prepared statements; never commit secrets or logs.
- DAOs share BaseDao and whitelist writable columns. User reads exclude password hashes.
- Keep tests in tests/, outside application routes. Use an isolated database for API tests.
- Services validate ownership, statuses, stock and price snapshots; grouped changes use transactions.
- Keep routes thin and update Swagger when endpoints change. JWT and frontend API integration follow in M4.
- External calls need timeouts, logged failures and friendly errors.
- JS names: camelCase; PHP classes: PascalCase; database names: snake_case.
- Follow the student's agreed requirements and current milestone.
- Record actual AI work and corrections.
- Discuss AI-log mistakes with the student before finalizing them.
- Do not commit or push without the student's approval.

