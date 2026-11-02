# PitStop OS - entity relationship diagram

Implemented in Milestone 2 in `pitstop_os.sql`. GitHub renders this Mermaid diagram. Minimum child counts and cross-table business rules remain service-layer responsibilities for M3.

```mermaid
erDiagram
    users ||--o{ vehicles : owns
    users ||--o{ service_requests : requests
    vehicles ||--o{ service_requests : receives
    services ||--o{ service_requests : selects
    service_requests ||--o| service_orders : produces
    users o|--o{ service_orders : assigned_mechanic
    service_orders ||--|{ service_order_items : contains
    services o|--o{ service_order_items : labor
    parts o|--o{ service_order_items : material
    users ||--o{ support_tickets : opens
    service_requests o|--o{ support_tickets : relates_to
    support_tickets ||--|{ ticket_messages : contains
    users ||--o{ ticket_messages : sends
    users {
        int id PK
        string full_name 
        string email UK
        string password_hash 
        string role 
        string city 
        datetime created_at 
    }
    vehicles {
        int id PK
        int user_id FK
        string vin UK
        string make 
        string model 
        int year 
        string license_plate UK
        int mileage 
        string fuel_type 
        string transmission 
        datetime created_at 
    }
    service_requests {
        int id PK
        int vehicle_id FK
        int requested_by FK
        int service_id FK
        text description 
        date preferred_date 
        string priority 
        string status 
        text notes 
        datetime created_at 
    }
    services {
        int id PK
        string name 
        text description 
        decimal standard_price 
        int estimated_duration 
    }
    service_orders {
        int id PK
        int service_request_id FK, UK
        int mechanic_id FK
        text diagnosis 
        string status 
        decimal estimated_cost 
        decimal final_cost 
        datetime created_at 
    }
    parts {
        int id PK
        string name 
        string sku UK
        string category 
        int quantity 
        decimal unit_price 
        int minimum_stock 
    }
    service_order_items {
        int id PK
        int service_order_id FK
        int service_id FK
        int part_id FK
        int quantity 
        decimal unit_price 
    }
    support_tickets {
        int id PK
        int customer_id FK
        int service_request_id FK
        string subject 
        text description 
        string category 
        string priority 
        string status 
        datetime created_at 
    }
    ticket_messages {
        int id PK
        int ticket_id FK
        int sender_id FK
        text message 
        datetime created_at 
    }
```

## Why these entities exist
- users: customer ownership and employee identity in a single role-aware model.
- vehicles: vehicle details independent of individual visits.
- service_requests: customer intent, preferred date and service progress.
- services: reusable labor catalog and initial pricing.
- service_orders: workshop assessment, assigned mechanic and costs.
- parts: stock catalog with low-stock thresholds.
- service_order_items: historical labor and material quantities/prices for a repair.
- support_tickets: customer support issue with optional service context.
- ticket_messages: chronological conversation with sender attribution.

## Constraints and decisions for Milestone 2
- Primary keys are positive auto-increment IDs; all foreign keys are indexed.
- `email`, `sku`, `license_plate` and non-null `vin` are unique. Empty VIN input becomes NULL in MySQL. VIN is optional; transmission and city extend the initial proposal to support the required UI.
- `service_requests.service_id` replaces free-text service_type with a catalog FK. A request selects one primary service; an order can contain multiple labor/part items.
- A request has zero or one order; enforce UNIQUE(service_request_id). An order can have no mechanic until assignment.
- users.role is admin, mechanic or customer. Service validation must enforce customer ownership and mechanic assignment roles; foreign keys alone do not enforce roles.
- requested_by must own the selected vehicle. A ticket's linked request must belong to its customer.
- Exactly one of service_id and part_id on each order item must be non-null (XOR). Quantity must be positive. Each order has at least one item, enforced transactionally by services.
- Stock, mileage, prices and costs must be nonnegative; duration must be positive. Money uses DECIMAL(10,2), never floating-point storage. Store item prices as historical snapshots.
- final_cost remains NULL until completion. Estimated cost is a quotation; final cost is the sum of order item quantities multiplied by snapshot prices. No tax or discount model is included in M1.
- Restrict deleting referenced customers, vehicles, services or parts. Preserve historical orders and conversations. Plan archiving instead of destructive history deletion.
- Requests: Requested, Approved, Diagnosing, In Progress, Ready for Pickup, Completed, Cancelled. Orders use the corresponding workshop subset. Update both in one transaction later.
- Tickets: Open, In Progress, Waiting for Customer, Resolved, Closed. A ticket begins with one message created in the same transaction.
- M2 sample passwords use bcrypt hashes; the static frontend still has no real authentication. UserDao excludes hashes from ordinary reads.
- M2 implements the schema and DAO layer. Business rules/transaction orchestration and routes follow in M3; actual authorization follows in M4.

## Frontend collection mapping
`users`, `vehicles`, `services`, `parts` retain their names. `requests` -> service_requests; `orders` -> service_orders; `items` -> service_order_items; `tickets` -> support_tickets; `messages` -> ticket_messages. These short frontend collection names are internal; API services will normalize responses.
