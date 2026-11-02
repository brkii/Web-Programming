# AGENTS.md

## Project
PitStop OS: automotive service and customer support SPA.
Roles: customer, admin, mechanic. Current scope: Milestone 2 (database and DAO).

## Technology stack
- Frontend: HTML, CSS, JavaScript, Bootstrap.
- Backend: PHP 8.2+, PDO and MySQL; FlightPHP routes follow in Milestone 3.

## Folder structure
- frontend/: css/, js/, views/, assets/ and index.html.
- backend/: routes/, services/, dao/, middleware/, config/, logs/.
- database/: ERD and SQL export with sample data.
- docs/: project documentation and AI_LOG.md.

## Rules
- Each screen has its own HTML view; shared CSS/JS load once.
- Keep frontend and backend separate. Support mobile, light and dark themes.
- Backend flow: route -> service -> DAO -> database. Only services call DAO.
- Use PDO prepared statements; never commit secrets or logs.
- DAOs share BaseDao and whitelist writable columns. User reads exclude password hashes.
- Keep M2 tests separate from application routes. Business rules and HTTP endpoints follow in M3.
- JS names: camelCase; PHP classes: PascalCase; database names: snake_case.
- Follow the student's agreed requirements and current milestone.
- Record actual AI work and corrections.
- Discuss AI-log mistakes with the student before finalizing them.

