# Milestone 1 implementation and verification

## Requirement mapping

| Requirement | Files / implementation | Status |
| --- | --- | --- |
| Separate frontend/backend structure | frontend/{css,js,views,assets}; backend/{routes,services,dao,middleware,config,logs} | Implemented |
| Ignore secrets, logs and generated dependencies | .gitignore | Implemented |
| Single entry point and no full-page navigation | frontend/index.html, js/router.js; hash routing and fetched partials | Implemented; browser back/forward checked |
| All relevant static screens | 21 separate HTML partials in frontend/views | Present; all screen types opened in browser |
| Landing, login, registration | home.html, login.html, register.html | Browser checked; login and password-match validation checked |
| Customer portal | Dashboard, vehicles, request form, requests, history, tickets, profile | Implemented |
| Workshop portal | Overview, customers, vehicles, requests, work orders, services, parts, support | Implemented |
| Mechanic role | mechanic-dashboard.html and shared workshop screens | Assigned dashboard opened in browser |
| Responsive layout and navigation | css/responsive.css; mobile menu | 390 px vehicle/profile layouts and mobile navigation checked |
| Reusable UI | js/components/ui.js and common shell in app.js | Cards, tables, badges, modals, forms, toasts, empty states and confirmation dialogs |
| Consistent mock data | js/mockData.js, js/services/store.js | Relationship tests passed; metrics derive from collections |
| Meaningful mock interactions | js/app.js and pages.js | Vehicle add/search/delete, request submit/approval/assignment, part CRUD and support reply checked in browser |
| External API feature placement | Vehicle dialog and customer dashboard | Clearly simulated VIN and weather previews |
| Draft ERD, at least 5 related entities | database/erd.md | 9 entities, Mermaid, PK/FK/cardinalities and constraints |
| README | README.md | Scope, architecture, roles, run steps, milestones and future links |
| AI context and log | AGENTS.md, docs/AI_LOG.md | Shortened; two mistake entries supplied by the student and added |
| Adapted agent files | .github/agents/{frontend,backend,reviewer}.agent.md | Created in the demonstrated course layout |
| AI log skill | .agents/skills/update-ai-log/SKILL.md | Created; frontmatter manually inspected. Bundled validator could not run because PyYAML is absent |
| Milestone branch | milestone1 | Created locally |
| PR/collaborator/reviewer | Course workflow documented in README | Not performed; requires GitHub workflow and late-submission coordination |

## Executed checks (2026-11-01)

- `node --check` on application/page modules: passed.
- `node --test tests/data.test.js`: all five tests passed in the final run.
- Local frontend served through Python HTTP server without PHP/MySQL.
- Landing and customer dashboard visually inspected at desktop width.
- Added Honda Accord using the demo VIN, searched for it, then deleted it.
- Submitted a service request, switched to admin, approved it and assigned the mechanic. New data survived SPA navigation and role changes in the same document.
- Opened customer, admin and mechanic screens, including vehicle detail, service history and ticket conversation.
- Added a support reply and verified the sender/message in the conversation.
- Added a part, edited its quantity across the minimum-stock threshold and deleted the test part.
- Verified browser back/forward between profile and support.
- Verified empty results after filtering vehicles to Available in the original seed dataset.
- Verified login password visibility, simulated login, mismatched registration passwords, then successful simulated registration.
- Verified repaired admin profile route and keyboard skip-link focus without changing the hash route.
- At a 390 px viewport, verified mobile menu opening, vehicle navigation, menu closing and no horizontal page overflow. Restored the normal viewport afterward.
- Browser error log was empty at the inspected checkpoint. This is not a claim of exhaustive cross-browser testing.

## Remaining review and explicit simulations

- Student should review the diff and practice explaining the code. The AI log includes the student's account of testing and corrections; the checks above record assistant verification separately.
- Only representative workflows were exercised; customer/service CRUD share the tested editor but were not each exhaustively exercised in the browser.
- No real database, DAO, FlightPHP routes, JWT, middleware, logging, Swagger, deployment, accounts or third-party API calls exist yet.
- Demo actions are in memory and reset on reload. Demo routing is not authorization.
- The revised interface removes the car illustration. Weather and VIN results remain fixed samples.
- The Mermaid source is provided for GitHub rendering; its diagram was not rendered in this local browser session.
- GitHub publishing, collaboration invitation and PR creation remain separate from this local implementation. Never merge into main automatically.

## Milestone 2 handoff

Implement the nine-entity schema with consistent FKs and sample data. Add ignored real config and a safe example. Build reusable BaseDao plus entity DAOs with prepared CRUD statements. Use decimal prices, nullable final costs, the item XOR constraint and a unique request-to-order FK. Keep database/DAO work separate from M3 services/routes and M4 authentication/API-connected UI.

## Design revision requested by the student
- Main sample user changed to Amina Brković.
- Agent instructions shortened to the lab-style format. Small local commits authorized on 2026-11-02; push remains subject to separate approval.
- AI log condensed; two mistake entries subsequently supplied by the student and added.
- Replaced the old logo and landing page. Removed the car artwork and floating slogan card from rendered screens.
- Removed the sidebar location box and repeated demo banners. Static limitations remain in contextual notes and documentation.
- Added persistent light/dark theme selection. Checked persistence after reload, light-mode vehicle form and both workspace themes.
- Mechanic dashboard now focuses on assigned jobs, without workshop revenue/customer summaries.

## Black/red visual revision
- Replaced the home-page garage card with generated performance-car imagery, a new wordmark/badge and red calls to action.
- Updated both themes with red accents; dark is the default for new visitors and an existing theme choice is preserved.
- Checked desktop and 390 px mobile framing, absence of horizontal overflow, theme switching and the Explore the platform action.
- Image provenance and generation prompt: hero-image-prompt.md.
