# AI Log

## Milestone 1

### Tools and tasks
Copilot assisted with the initial implementation and subsequent changes according to the student's requirements.

Examples of tasks:
- Preparing and adjusting frontend views and shared components.
- Checking sample-data relationships and SPA navigation.
- Helping create and refine the PitStop OS logo, following the student's design instructions and feedback.

### Important prompts
- "Add a light mode option to the PitStop OS frontend while keeping the existing dark theme as the default. Make sure the layout, text, and components remain readable in both themes."

- "Use the structure and coding approach from the lab materials I provided as a reference. Keep the project consistent with the professor's requirements, especially the SPA structure, Bootstrap 5, and separation of HTML, CSS, and JavaScript."
- "Create a responsive sidebar navigation for both the customer and admin dashboards using Bootstrap 5. Make sure the navigation works correctly on mobile devices and follows the existing PitStop OS design. Do not change any unrelated files."

### Review and corrections

- **Agent setup:** I asked Copilot to simplify the initial agent configuration and keep only the necessary instructions, following the structure provided in the lab materials.

- **Landing page design:** After reviewing the generated frontend, I requested changes to the logo and landing page because the initial design did not match the style I wanted for PitStop OS.

- **Unnecessary UI elements:** I asked Copilot to remove the car illustration and floating slogan card to make the landing page cleaner and more professional.

- **Theme improvement:** I requested a light mode option in addition to the existing dark theme, making sure the text, navigation, and dashboard components remained readable in both modes.

### Where the AI was wrong

#### AI Mistake 1 – SPA Navigation Issue

**Problem:** During the initial frontend generation, Copilot created navigation links that reloaded the entire page instead of switching views dynamically.

**How I noticed it:** While testing the application, I noticed that clicking sidebar links refreshed the browser.

**How I fixed it:** I asked Copilot to update the routing logic in JavaScript so that the application could switch between views without a full page reload.

#### AI Mistake 2 – Responsive Design Issue

**Problem:** The generated dashboard looked good on desktop, but some cards and the sidebar did not display correctly on smaller screens.

**How I noticed it:** I tested the application using the browser's responsive device mode and noticed that some elements overflowed the screen.

**How I fixed it:** I adjusted the responsive CSS and asked Copilot to improve the Bootstrap layout so that the sidebar and dashboard cards worked correctly on mobile devices.

### Checks
Frontend checks cover navigation, sample-data relationships and escaping. Test commands are listed in README.md.

## Milestone 2

### Tools and tasks
Copilot helped implement the Milestone 2 SQL export,and checked them on MySQL and XAMPP MariaDB.

### Important prompt
- "Review the sample data in pitstop_os.sql and make sure it includes realistic vehicles, customers, service requests, and repair records. Keep the data consistent so that each vehicle belongs to the correct customer and the related service records make sense."

- "Update the DAO classes to include methods for retrieving a customer's vehicles and their service history. Follow the existing BaseDao structure and keep the queries inside the DAO layer. Do not modify the frontend or implement API routes yet."

### Where the AI was wrong
Student-provided reflection:

**1. Incorrect service order cost calculation**

- **What was wrong:** Copilot initially designed the `service_order_items` table without storing the unit price for each item, which would make it difficult to preserve the original repair costs.
- **How I noticed it:** While reviewing the database structure, I realized that changing the price of a service or spare part could also affect how historical repair costs were calculated.
- **How I fixed it:** I asked Copilot to add a `unit_price` column to `service_order_items` so that each work order keeps the price used at the time of the repair.

**2. Missing support ticket relationship**

- **What was wrong:** Copilot initially created the support_tickets table without a relationship to service_requests.
- **How I noticed it:** When reviewing the ERD, I noticed that customers could open support tickets, but the tickets could not be connected to a specific service request.
- **How I fixed it:** I asked Copilot to add a nullable service_request_id foreign key to support_tickets, allowing tickets to be linked to repairs when needed.

### Checks
PHP syntax, SQL import, CRUD, constraints, prepared statements and rollback were checked. Test commands are listed in README.md.

## Milestone 3

### Tools and tasks

Copilot was used to assist with the service layer, FlightPHP CRUD routes, Swagger documentation, and NHTSA VIN decoding integration, following the project requirements and provided lab materials.

### Important prompts

- "Can you check if the service layer follows the correct structure and uses the existing DAO classes?"
- "Some endpoints are not showing up in Swagger. Can you check what might be causing this?"
- "Can you help me check the VIN decoding functionality and how invalid responses are handled?"
- "I'm getting an error when cancelling an order. Can you help me find the problem without changing unrelated code?"

### Where the AI was wrong

**1. Swagger documentation issue**

- **What was wrong:** An unsupported annotation parameter caused some API endpoints to be missing from the generated Swagger documentation.
- **How it was noticed:** While checking Swagger UI, I noticed that not all endpoints were displayed correctly.
- **How it was fixed:** The unsupported parameter was removed and the OpenAPI documentation was generated again.

**2. Stock validation issue**

- **What was wrong:** The stock quantity was not properly validated when reserved parts were returned after an order cancellation.
- **How it was noticed:** Testing an order cancellation with a high stock quantity resulted in a database error.
- **How it was fixed:** An additional stock limit check was added to prevent invalid updates, and rollback was used to keep the data consistent.

### Checks

Tested CRUD operations through Swagger, including creating, retrieving, updating, and deleting service records. Also checked invalid input values, OpenAPI documentation, VIN decoding, and external API error handling.
