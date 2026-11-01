# AI Log

## Milestone 1

### Tools and tasks
Codex assisted with the initial implementation and subsequent changes according to the student's requirements.

Examples of tasks:
- Organizing project folders and .gitignore.
- Preparing and adjusting frontend views and shared components.
- Checking sample-data relationships and SPA navigation.
- Adjusting the design following the student's review.

### Important prompts
- "samo prati onu formu kako je trazeno od profesorice vezano za podjelu fajlova kako ce izgledati"
- "kao glavnu osobu stavi Amina Brković"
- "moramo se pobrinuti da ima light mode"
- "nek je slicno kao na onom labu sto sam ti poslala"

### Review and corrections
The student requested a shorter agent setup, a different logo and landing page, removal of the car illustration and floating slogan card, and a light theme.

### Where the AI was wrong

#### AI Mistake 1 – SPA Navigation Issue

**Problem:** During the initial frontend generation, Codex created navigation links that reloaded the entire page instead of switching views dynamically.

**How I noticed it:** While testing the application, I noticed that clicking sidebar links refreshed the browser.

**How I fixed it:** I asked Codex to update the routing logic in JavaScript so that the application could switch between views without a full page reload.

#### AI Mistake 2 – Responsive Design Issue

**Problem:** The generated dashboard looked good on desktop, but some cards and the sidebar did not display correctly on smaller screens.

**How I noticed it:** I tested the application using the browser's responsive device mode and noticed that some elements overflowed the screen.

**How I fixed it:** I adjusted the responsive CSS and asked Codex to improve the Bootstrap layout so that the sidebar and dashboard cards worked correctly on mobile devices.

### Checks
Automated checks and browser verification are listed in MILESTONE1_CHECKLIST.md. These are assistant checks; student review is separate.
