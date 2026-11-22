<?php
declare(strict_types=1);
use OpenApi\Attributes as OA;

#[OA\Info(title: 'PitStop OS API', version: '1.0.0', description: 'Milestone 3: service logic, CRUD and VIN lookup. Local development API; JWT authentication and authorization follow in M4. PUT accepts a partial object; omitted fields retain their values. Unknown and read-only fields are rejected. Money is returned as decimal strings.', contact: new OA\Contact(name: 'Amina Brković'))]
#[OA\Server(url: API_BASE_URL, description: 'Current installation')]
#[OA\Schema(schema: 'Error', type: 'object', required: ['error'], properties: [new OA\Property(property: 'error', type: 'string', example: 'Record not found.')])]
#[OA\Schema(schema: 'Message', type: 'object', required: ['message'], properties: [new OA\Property(property: 'message', type: 'string', example: 'Record deleted.')])]
#[OA\Schema(schema: 'UserInput', type: 'object', description: 'Passwords are hashed and never returned. Role changes are blocked when the user has activity. Authentication follows in M4.', additionalProperties: false, required: ['full_name', 'email', 'password', 'city'], properties: [
    new OA\Property(property: 'full_name', type: 'string', maxLength: 100, minLength: 1, example: 'Amina Brković'),
    new OA\Property(property: 'email', type: 'string', maxLength: 190, minLength: 1, format: 'email', example: 'new.customer@example.test'),
    new OA\Property(property: 'password', type: 'string', maxLength: 72, minLength: 1, format: 'password', writeOnly: true, description: '8–72 bytes, at least one letter and one digit.', example: 'PitStop123!'),
    new OA\Property(property: 'role', type: 'string', enum: ['customer', 'admin', 'mechanic'], example: 'customer'),
    new OA\Property(property: 'city', type: 'string', maxLength: 100, minLength: 1, example: 'Sarajevo')
])]
#[OA\Schema(schema: 'UserUpdate', type: 'object', description: 'Passwords are hashed and never returned. Role changes are blocked when the user has activity. Authentication follows in M4.', additionalProperties: false, minProperties: 1, properties: [
    new OA\Property(property: 'full_name', type: 'string', maxLength: 100, minLength: 1, example: 'Amina Brković'),
    new OA\Property(property: 'email', type: 'string', maxLength: 190, minLength: 1, format: 'email', example: 'new.customer@example.test'),
    new OA\Property(property: 'password', type: 'string', maxLength: 72, minLength: 1, format: 'password', writeOnly: true, description: '8–72 bytes, at least one letter and one digit.', example: 'PitStop123!'),
    new OA\Property(property: 'role', type: 'string', enum: ['customer', 'admin', 'mechanic'], example: 'customer'),
    new OA\Property(property: 'city', type: 'string', maxLength: 100, minLength: 1, example: 'Sarajevo')
])]
#[OA\Schema(schema: 'User', type: 'object', description: 'Passwords are hashed and never returned. Role changes are blocked when the user has activity. Authentication follows in M4.', additionalProperties: false, required: ['full_name', 'email', 'role', 'city', 'id', 'created_at'], properties: [
    new OA\Property(property: 'id', type: 'integer', minimum: 1, example: 1),
    new OA\Property(property: 'full_name', type: 'string', maxLength: 100, minLength: 1, example: 'Amina Brković'),
    new OA\Property(property: 'email', type: 'string', maxLength: 190, minLength: 1, format: 'email', example: 'new.customer@example.test'),
    new OA\Property(property: 'role', type: 'string', enum: ['customer', 'admin', 'mechanic'], example: 'customer'),
    new OA\Property(property: 'city', type: 'string', maxLength: 100, minLength: 1, example: 'Sarajevo'),
    new OA\Property(property: 'created_at', type: 'string', example: '2026-11-22 12:00:00', description: 'Database timestamp, YYYY-MM-DD HH:MM:SS.')
])]
#[OA\Schema(schema: 'VehicleInput', type: 'object', description: 'The owner must be a customer. VIN and plate are unique. Owner changes are blocked once a vehicle has requests.', additionalProperties: false, required: ['user_id', 'make', 'model', 'year', 'license_plate', 'fuel_type', 'transmission'], properties: [
    new OA\Property(property: 'user_id', type: 'integer', minimum: 1, maximum: 2147483647, example: 1),
    new OA\Property(property: 'vin', type: 'string', nullable: true, pattern: '^[A-HJ-NPR-Z0-9]{17}$', example: null),
    new OA\Property(property: 'make', type: 'string', maxLength: 100, minLength: 1, example: 'Volvo'),
    new OA\Property(property: 'model', type: 'string', maxLength: 100, minLength: 1, example: 'XC60'),
    new OA\Property(property: 'year', type: 'integer', minimum: 1886, maximum: 2155, example: 2022),
    new OA\Property(property: 'license_plate', type: 'string', maxLength: 30, minLength: 1, example: 'M33-T-001'),
    new OA\Property(property: 'mileage', type: 'integer', minimum: 0, maximum: 2147483647, example: 42850),
    new OA\Property(property: 'fuel_type', type: 'string', enum: ['Petrol', 'Diesel', 'Hybrid', 'Electric'], example: 'Hybrid'),
    new OA\Property(property: 'transmission', type: 'string', enum: ['Automatic', 'Manual'], example: 'Automatic')
])]
#[OA\Schema(schema: 'VehicleUpdate', type: 'object', description: 'The owner must be a customer. VIN and plate are unique. Owner changes are blocked once a vehicle has requests.', additionalProperties: false, minProperties: 1, properties: [
    new OA\Property(property: 'user_id', type: 'integer', minimum: 1, maximum: 2147483647, example: 1),
    new OA\Property(property: 'vin', type: 'string', nullable: true, pattern: '^[A-HJ-NPR-Z0-9]{17}$', example: null),
    new OA\Property(property: 'make', type: 'string', maxLength: 100, minLength: 1, example: 'Volvo'),
    new OA\Property(property: 'model', type: 'string', maxLength: 100, minLength: 1, example: 'XC60'),
    new OA\Property(property: 'year', type: 'integer', minimum: 1886, maximum: 2155, example: 2022),
    new OA\Property(property: 'license_plate', type: 'string', maxLength: 30, minLength: 1, example: 'M33-T-001'),
    new OA\Property(property: 'mileage', type: 'integer', minimum: 0, maximum: 2147483647, example: 42850),
    new OA\Property(property: 'fuel_type', type: 'string', enum: ['Petrol', 'Diesel', 'Hybrid', 'Electric'], example: 'Hybrid'),
    new OA\Property(property: 'transmission', type: 'string', enum: ['Automatic', 'Manual'], example: 'Automatic')
])]
#[OA\Schema(schema: 'Vehicle', type: 'object', description: 'The owner must be a customer. VIN and plate are unique. Owner changes are blocked once a vehicle has requests.', additionalProperties: false, required: ['user_id', 'vin', 'make', 'model', 'year', 'license_plate', 'mileage', 'fuel_type', 'transmission', 'id', 'created_at'], properties: [
    new OA\Property(property: 'id', type: 'integer', minimum: 1, example: 1),
    new OA\Property(property: 'user_id', type: 'integer', minimum: 1, maximum: 2147483647, example: 1),
    new OA\Property(property: 'vin', type: 'string', nullable: true, pattern: '^[A-HJ-NPR-Z0-9]{17}$', example: null),
    new OA\Property(property: 'make', type: 'string', maxLength: 100, minLength: 1, example: 'Volvo'),
    new OA\Property(property: 'model', type: 'string', maxLength: 100, minLength: 1, example: 'XC60'),
    new OA\Property(property: 'year', type: 'integer', minimum: 1886, maximum: 2155, example: 2022),
    new OA\Property(property: 'license_plate', type: 'string', maxLength: 30, minLength: 1, example: 'M33-T-001'),
    new OA\Property(property: 'mileage', type: 'integer', minimum: 0, maximum: 2147483647, example: 42850),
    new OA\Property(property: 'fuel_type', type: 'string', enum: ['Petrol', 'Diesel', 'Hybrid', 'Electric'], example: 'Hybrid'),
    new OA\Property(property: 'transmission', type: 'string', enum: ['Automatic', 'Manual'], example: 'Automatic'),
    new OA\Property(property: 'created_at', type: 'string', example: '2026-11-22 12:00:00', description: 'Database timestamp, YYYY-MM-DD HH:MM:SS.')
])]
#[OA\Schema(schema: 'ServiceInput', type: 'object', description: 'Prices are catalog prices. Changing them never rewrites existing order item snapshots.', additionalProperties: false, required: ['name', 'description', 'standard_price', 'estimated_duration'], properties: [
    new OA\Property(property: 'name', type: 'string', maxLength: 100, minLength: 1, example: 'Wheel alignment'),
    new OA\Property(property: 'description', type: 'string', maxLength: 10000, minLength: 1, example: 'Check and adjust wheel alignment.'),
    new OA\Property(property: 'standard_price', oneOf: [new OA\Schema(type: 'string', pattern: '^\\d{1,8}(?:\\.\\d{1,2})?$'), new OA\Schema(type: 'number', minimum: 0, maximum: 99999999.99)], example: '60.00'),
    new OA\Property(property: 'estimated_duration', type: 'integer', minimum: 1, maximum: 2147483647, example: 45)
])]
#[OA\Schema(schema: 'ServiceUpdate', type: 'object', description: 'Prices are catalog prices. Changing them never rewrites existing order item snapshots.', additionalProperties: false, minProperties: 1, properties: [
    new OA\Property(property: 'name', type: 'string', maxLength: 100, minLength: 1, example: 'Wheel alignment'),
    new OA\Property(property: 'description', type: 'string', maxLength: 10000, minLength: 1, example: 'Check and adjust wheel alignment.'),
    new OA\Property(property: 'standard_price', oneOf: [new OA\Schema(type: 'string', pattern: '^\\d{1,8}(?:\\.\\d{1,2})?$'), new OA\Schema(type: 'number', minimum: 0, maximum: 99999999.99)], example: '60.00'),
    new OA\Property(property: 'estimated_duration', type: 'integer', minimum: 1, maximum: 2147483647, example: 45)
])]
#[OA\Schema(schema: 'Service', type: 'object', description: 'Prices are catalog prices. Changing them never rewrites existing order item snapshots.', additionalProperties: false, required: ['name', 'description', 'standard_price', 'estimated_duration', 'id'], properties: [
    new OA\Property(property: 'id', type: 'integer', minimum: 1, example: 1),
    new OA\Property(property: 'name', type: 'string', maxLength: 100, minLength: 1, example: 'Wheel alignment'),
    new OA\Property(property: 'description', type: 'string', maxLength: 10000, minLength: 1, example: 'Check and adjust wheel alignment.'),
    new OA\Property(property: 'standard_price', type: 'string', pattern: '^\\d{1,8}\\.\\d{2}$', example: '60.00'),
    new OA\Property(property: 'estimated_duration', type: 'integer', minimum: 1, maximum: 2147483647, example: 45)
])]
#[OA\Schema(schema: 'PartInput', type: 'object', description: 'Quantity is available stock. Adding parts to an order reserves stock; removal or cancellation returns it.', additionalProperties: false, required: ['name', 'sku', 'category', 'unit_price'], properties: [
    new OA\Property(property: 'name', type: 'string', maxLength: 100, minLength: 1, example: 'Air filter'),
    new OA\Property(property: 'sku', type: 'string', maxLength: 100, minLength: 1, example: 'M3-AIR-001'),
    new OA\Property(property: 'category', type: 'string', maxLength: 100, minLength: 1, example: 'Filters'),
    new OA\Property(property: 'quantity', type: 'integer', minimum: 0, maximum: 2147483647, example: 10),
    new OA\Property(property: 'unit_price', oneOf: [new OA\Schema(type: 'string', pattern: '^\\d{1,8}(?:\\.\\d{1,2})?$'), new OA\Schema(type: 'number', minimum: 0, maximum: 99999999.99)], example: '15.50'),
    new OA\Property(property: 'minimum_stock', type: 'integer', minimum: 0, maximum: 2147483647, example: 2)
])]
#[OA\Schema(schema: 'PartUpdate', type: 'object', description: 'Quantity is available stock. Adding parts to an order reserves stock; removal or cancellation returns it.', additionalProperties: false, minProperties: 1, properties: [
    new OA\Property(property: 'name', type: 'string', maxLength: 100, minLength: 1, example: 'Air filter'),
    new OA\Property(property: 'sku', type: 'string', maxLength: 100, minLength: 1, example: 'M3-AIR-001'),
    new OA\Property(property: 'category', type: 'string', maxLength: 100, minLength: 1, example: 'Filters'),
    new OA\Property(property: 'quantity', type: 'integer', minimum: 0, maximum: 2147483647, example: 10),
    new OA\Property(property: 'unit_price', oneOf: [new OA\Schema(type: 'string', pattern: '^\\d{1,8}(?:\\.\\d{1,2})?$'), new OA\Schema(type: 'number', minimum: 0, maximum: 99999999.99)], example: '15.50'),
    new OA\Property(property: 'minimum_stock', type: 'integer', minimum: 0, maximum: 2147483647, example: 2)
])]
#[OA\Schema(schema: 'Part', type: 'object', description: 'Quantity is available stock. Adding parts to an order reserves stock; removal or cancellation returns it.', additionalProperties: false, required: ['name', 'sku', 'category', 'quantity', 'unit_price', 'minimum_stock', 'id'], properties: [
    new OA\Property(property: 'id', type: 'integer', minimum: 1, example: 1),
    new OA\Property(property: 'name', type: 'string', maxLength: 100, minLength: 1, example: 'Air filter'),
    new OA\Property(property: 'sku', type: 'string', maxLength: 100, minLength: 1, example: 'M3-AIR-001'),
    new OA\Property(property: 'category', type: 'string', maxLength: 100, minLength: 1, example: 'Filters'),
    new OA\Property(property: 'quantity', type: 'integer', minimum: 0, maximum: 2147483647, example: 10),
    new OA\Property(property: 'unit_price', type: 'string', pattern: '^\\d{1,8}\\.\\d{2}$', example: '15.50'),
    new OA\Property(property: 'minimum_stock', type: 'integer', minimum: 0, maximum: 2147483647, example: 2)
])]
#[OA\Schema(schema: 'ServiceRequestInput', type: 'object', description: 'New requests start Requested. Requester must own the vehicle. Once an order exists, change status on the order. Completed/cancelled requests are immutable.', additionalProperties: false, required: ['vehicle_id', 'requested_by', 'service_id', 'description', 'preferred_date'], properties: [
    new OA\Property(property: 'vehicle_id', type: 'integer', minimum: 1, maximum: 2147483647, example: 1),
    new OA\Property(property: 'requested_by', type: 'integer', minimum: 1, maximum: 2147483647, example: 1),
    new OA\Property(property: 'service_id', type: 'integer', minimum: 1, maximum: 2147483647, example: 1),
    new OA\Property(property: 'description', type: 'string', maxLength: 10000, minLength: 1, example: 'Annual maintenance.'),
    new OA\Property(property: 'preferred_date', type: 'string', format: 'date', example: '2026-12-01'),
    new OA\Property(property: 'priority', type: 'string', enum: ['Normal', 'High', 'Urgent'], example: 'Normal'),
    new OA\Property(property: 'status', type: 'string', enum: ['Requested'], example: 'Requested'),
    new OA\Property(property: 'notes', type: 'string', maxLength: 10000, minLength: 1, nullable: true, example: null)
])]
#[OA\Schema(schema: 'ServiceRequestUpdate', type: 'object', description: 'New requests start Requested. Requester must own the vehicle. Once an order exists, change status on the order. Completed/cancelled requests are immutable.', additionalProperties: false, minProperties: 1, properties: [
    new OA\Property(property: 'vehicle_id', type: 'integer', minimum: 1, maximum: 2147483647, example: 1),
    new OA\Property(property: 'requested_by', type: 'integer', minimum: 1, maximum: 2147483647, example: 1),
    new OA\Property(property: 'service_id', type: 'integer', minimum: 1, maximum: 2147483647, example: 1),
    new OA\Property(property: 'description', type: 'string', maxLength: 10000, minLength: 1, example: 'Annual maintenance.'),
    new OA\Property(property: 'preferred_date', type: 'string', format: 'date', example: '2026-12-01'),
    new OA\Property(property: 'priority', type: 'string', enum: ['Normal', 'High', 'Urgent'], example: 'Normal'),
    new OA\Property(property: 'status', type: 'string', enum: ['Requested', 'Approved', 'Diagnosing', 'In Progress', 'Ready for Pickup', 'Completed', 'Cancelled'], example: 'Requested'),
    new OA\Property(property: 'notes', type: 'string', maxLength: 10000, minLength: 1, nullable: true, example: null)
])]
#[OA\Schema(schema: 'ServiceRequest', type: 'object', description: 'New requests start Requested. Requester must own the vehicle. Once an order exists, change status on the order. Completed/cancelled requests are immutable.', additionalProperties: false, required: ['vehicle_id', 'requested_by', 'service_id', 'description', 'preferred_date', 'priority', 'status', 'notes', 'id', 'created_at'], properties: [
    new OA\Property(property: 'id', type: 'integer', minimum: 1, example: 1),
    new OA\Property(property: 'vehicle_id', type: 'integer', minimum: 1, maximum: 2147483647, example: 1),
    new OA\Property(property: 'requested_by', type: 'integer', minimum: 1, maximum: 2147483647, example: 1),
    new OA\Property(property: 'service_id', type: 'integer', minimum: 1, maximum: 2147483647, example: 1),
    new OA\Property(property: 'description', type: 'string', maxLength: 10000, minLength: 1, example: 'Annual maintenance.'),
    new OA\Property(property: 'preferred_date', type: 'string', format: 'date', example: '2026-12-01'),
    new OA\Property(property: 'priority', type: 'string', enum: ['Normal', 'High', 'Urgent'], example: 'Normal'),
    new OA\Property(property: 'status', type: 'string', enum: ['Requested', 'Approved', 'Diagnosing', 'In Progress', 'Ready for Pickup', 'Completed', 'Cancelled'], example: 'Requested'),
    new OA\Property(property: 'notes', type: 'string', maxLength: 10000, minLength: 1, nullable: true, example: null),
    new OA\Property(property: 'created_at', type: 'string', example: '2026-11-22 12:00:00', description: 'Database timestamp, YYYY-MM-DD HH:MM:SS.')
])]
#[OA\Schema(schema: 'ServiceOrderInput', type: 'object', description: 'Creation adds the primary service as the first item in one transaction. Status starts Approved. Progress: Approved → Diagnosing/In Progress → Ready for Pickup → Completed. Work requires a mechanic. Cancellation is allowed before Ready for Pickup and returns reserved parts. Request status is synchronized. Completion computes final_cost from snapshot prices. Completed/cancelled orders are immutable. Only unstarted Approved orders can be deleted.', additionalProperties: false, required: ['service_request_id'], properties: [
    new OA\Property(property: 'service_request_id', type: 'integer', minimum: 1, maximum: 2147483647, example: 2),
    new OA\Property(property: 'mechanic_id', type: 'integer', minimum: 1, maximum: 2147483647, nullable: true, example: 4),
    new OA\Property(property: 'diagnosis', type: 'string', maxLength: 10000, minLength: 1, nullable: true, example: null),
    new OA\Property(property: 'status', type: 'string', enum: ['Approved'], example: 'Approved'),
    new OA\Property(property: 'estimated_cost', oneOf: [new OA\Schema(type: 'string', pattern: '^\\d{1,8}(?:\\.\\d{1,2})?$'), new OA\Schema(type: 'number', minimum: 0, maximum: 99999999.99)], example: '120.00')
])]
#[OA\Schema(schema: 'ServiceOrderUpdate', type: 'object', description: 'Creation adds the primary service as the first item in one transaction. Status starts Approved. Progress: Approved → Diagnosing/In Progress → Ready for Pickup → Completed. Work requires a mechanic. Cancellation is allowed before Ready for Pickup and returns reserved parts. Request status is synchronized. Completion computes final_cost from snapshot prices. Completed/cancelled orders are immutable. Only unstarted Approved orders can be deleted.', additionalProperties: false, minProperties: 1, properties: [
    new OA\Property(property: 'service_request_id', type: 'integer', minimum: 1, maximum: 2147483647, example: 2),
    new OA\Property(property: 'mechanic_id', type: 'integer', minimum: 1, maximum: 2147483647, nullable: true, example: 4),
    new OA\Property(property: 'diagnosis', type: 'string', maxLength: 10000, minLength: 1, nullable: true, example: null),
    new OA\Property(property: 'status', type: 'string', enum: ['Approved', 'Diagnosing', 'In Progress', 'Ready for Pickup', 'Completed', 'Cancelled'], example: 'Approved'),
    new OA\Property(property: 'estimated_cost', oneOf: [new OA\Schema(type: 'string', pattern: '^\\d{1,8}(?:\\.\\d{1,2})?$'), new OA\Schema(type: 'number', minimum: 0, maximum: 99999999.99)], example: '120.00')
])]
#[OA\Schema(schema: 'ServiceOrder', type: 'object', description: 'Creation adds the primary service as the first item in one transaction. Status starts Approved. Progress: Approved → Diagnosing/In Progress → Ready for Pickup → Completed. Work requires a mechanic. Cancellation is allowed before Ready for Pickup and returns reserved parts. Request status is synchronized. Completion computes final_cost from snapshot prices. Completed/cancelled orders are immutable. Only unstarted Approved orders can be deleted.', additionalProperties: false, required: ['service_request_id', 'mechanic_id', 'diagnosis', 'status', 'estimated_cost', 'final_cost', 'id', 'created_at'], properties: [
    new OA\Property(property: 'id', type: 'integer', minimum: 1, example: 1),
    new OA\Property(property: 'service_request_id', type: 'integer', minimum: 1, maximum: 2147483647, example: 2),
    new OA\Property(property: 'mechanic_id', type: 'integer', minimum: 1, maximum: 2147483647, nullable: true, example: 4),
    new OA\Property(property: 'diagnosis', type: 'string', maxLength: 10000, minLength: 1, nullable: true, example: null),
    new OA\Property(property: 'status', type: 'string', enum: ['Approved', 'Diagnosing', 'In Progress', 'Ready for Pickup', 'Completed', 'Cancelled'], example: 'Approved'),
    new OA\Property(property: 'estimated_cost', type: 'string', pattern: '^\\d{1,8}\\.\\d{2}$', example: '120.00'),
    new OA\Property(property: 'final_cost', type: 'string', nullable: true, pattern: '^\\d{1,8}\\.\\d{2}$', example: '120.00'),
    new OA\Property(property: 'created_at', type: 'string', example: '2026-11-22 12:00:00', description: 'Database timestamp, YYYY-MM-DD HH:MM:SS.')
])]
#[OA\Schema(schema: 'ServiceOrderItemInput', type: 'object', description: 'Exactly one of service_id or part_id must be non-null. Price is captured from the catalog by the server. Updates can change quantity only and retain the original price. Items are editable only before Ready for Pickup. The last item cannot be deleted.', additionalProperties: false, required: ['service_order_id', 'quantity'], properties: [
    new OA\Property(property: 'service_order_id', type: 'integer', minimum: 1, maximum: 2147483647, example: 1),
    new OA\Property(property: 'service_id', type: 'integer', minimum: 1, maximum: 2147483647, nullable: true, example: null),
    new OA\Property(property: 'part_id', type: 'integer', minimum: 1, maximum: 2147483647, nullable: true, example: 1),
    new OA\Property(property: 'quantity', type: 'integer', minimum: 1, maximum: 1000000, example: 1)
])]
#[OA\Schema(schema: 'ServiceOrderItemUpdate', type: 'object', description: 'Exactly one of service_id or part_id must be non-null. Price is captured from the catalog by the server. Updates can change quantity only and retain the original price. Items are editable only before Ready for Pickup. The last item cannot be deleted.', additionalProperties: false, minProperties: 1, properties: [
    new OA\Property(property: 'service_order_id', type: 'integer', minimum: 1, maximum: 2147483647, example: 1),
    new OA\Property(property: 'service_id', type: 'integer', minimum: 1, maximum: 2147483647, nullable: true, example: null),
    new OA\Property(property: 'part_id', type: 'integer', minimum: 1, maximum: 2147483647, nullable: true, example: 1),
    new OA\Property(property: 'quantity', type: 'integer', minimum: 1, maximum: 1000000, example: 1)
])]
#[OA\Schema(schema: 'ServiceOrderItem', type: 'object', description: 'Exactly one of service_id or part_id must be non-null. Price is captured from the catalog by the server. Updates can change quantity only and retain the original price. Items are editable only before Ready for Pickup. The last item cannot be deleted.', additionalProperties: false, required: ['service_order_id', 'service_id', 'part_id', 'quantity', 'unit_price', 'id'], properties: [
    new OA\Property(property: 'id', type: 'integer', minimum: 1, example: 1),
    new OA\Property(property: 'service_order_id', type: 'integer', minimum: 1, maximum: 2147483647, example: 1),
    new OA\Property(property: 'service_id', type: 'integer', minimum: 1, maximum: 2147483647, nullable: true, example: null),
    new OA\Property(property: 'part_id', type: 'integer', minimum: 1, maximum: 2147483647, nullable: true, example: 1),
    new OA\Property(property: 'quantity', type: 'integer', minimum: 1, maximum: 1000000, example: 1),
    new OA\Property(property: 'unit_price', type: 'string', pattern: '^\\d{1,8}\\.\\d{2}$', example: '12.00')
])]
#[OA\Schema(schema: 'SupportTicketInput', type: 'object', description: 'Creation also adds the opening message from description. A linked request must belong to the customer. Customer and opening description cannot change. Closed tickets are immutable; only unanswered Open tickets can be deleted.', additionalProperties: false, required: ['customer_id', 'subject', 'description', 'category'], properties: [
    new OA\Property(property: 'customer_id', type: 'integer', minimum: 1, maximum: 2147483647, example: 1),
    new OA\Property(property: 'service_request_id', type: 'integer', minimum: 1, maximum: 2147483647, nullable: true, example: 1),
    new OA\Property(property: 'subject', type: 'string', maxLength: 200, minLength: 1, example: 'Service appointment'),
    new OA\Property(property: 'description', type: 'string', maxLength: 10000, minLength: 1, example: 'Please confirm the appointment time.'),
    new OA\Property(property: 'category', type: 'string', maxLength: 100, minLength: 1, example: 'Service'),
    new OA\Property(property: 'priority', type: 'string', enum: ['Normal', 'High', 'Urgent'], example: 'Normal'),
    new OA\Property(property: 'status', type: 'string', enum: ['Open'], example: 'Open')
])]
#[OA\Schema(schema: 'SupportTicketUpdate', type: 'object', description: 'Creation also adds the opening message from description. A linked request must belong to the customer. Customer and opening description cannot change. Closed tickets are immutable; only unanswered Open tickets can be deleted.', additionalProperties: false, minProperties: 1, properties: [
    new OA\Property(property: 'customer_id', type: 'integer', minimum: 1, maximum: 2147483647, example: 1),
    new OA\Property(property: 'service_request_id', type: 'integer', minimum: 1, maximum: 2147483647, nullable: true, example: 1),
    new OA\Property(property: 'subject', type: 'string', maxLength: 200, minLength: 1, example: 'Service appointment'),
    new OA\Property(property: 'description', type: 'string', maxLength: 10000, minLength: 1, example: 'Please confirm the appointment time.'),
    new OA\Property(property: 'category', type: 'string', maxLength: 100, minLength: 1, example: 'Service'),
    new OA\Property(property: 'priority', type: 'string', enum: ['Normal', 'High', 'Urgent'], example: 'Normal'),
    new OA\Property(property: 'status', type: 'string', enum: ['Open', 'In Progress', 'Waiting for Customer', 'Resolved', 'Closed'], example: 'Open')
])]
#[OA\Schema(schema: 'SupportTicket', type: 'object', description: 'Creation also adds the opening message from description. A linked request must belong to the customer. Customer and opening description cannot change. Closed tickets are immutable; only unanswered Open tickets can be deleted.', additionalProperties: false, required: ['customer_id', 'service_request_id', 'subject', 'description', 'category', 'priority', 'status', 'id', 'created_at'], properties: [
    new OA\Property(property: 'id', type: 'integer', minimum: 1, example: 1),
    new OA\Property(property: 'customer_id', type: 'integer', minimum: 1, maximum: 2147483647, example: 1),
    new OA\Property(property: 'service_request_id', type: 'integer', minimum: 1, maximum: 2147483647, nullable: true, example: 1),
    new OA\Property(property: 'subject', type: 'string', maxLength: 200, minLength: 1, example: 'Service appointment'),
    new OA\Property(property: 'description', type: 'string', maxLength: 10000, minLength: 1, example: 'Please confirm the appointment time.'),
    new OA\Property(property: 'category', type: 'string', maxLength: 100, minLength: 1, example: 'Service'),
    new OA\Property(property: 'priority', type: 'string', enum: ['Normal', 'High', 'Urgent'], example: 'Normal'),
    new OA\Property(property: 'status', type: 'string', enum: ['Open', 'In Progress', 'Waiting for Customer', 'Resolved', 'Closed'], example: 'Open'),
    new OA\Property(property: 'created_at', type: 'string', example: '2026-11-22 12:00:00', description: 'Database timestamp, YYYY-MM-DD HH:MM:SS.')
])]
#[OA\Schema(schema: 'TicketMessageInput', type: 'object', description: 'A customer may only reply to their own ticket; staff may reply to any open conversation. Resolved/Closed tickets reject replies. Only the latest reply can be edited or deleted; the opening message is preserved. ticket_id and sender_id cannot change. These relationship checks do not authenticate the caller (M4).', additionalProperties: false, required: ['ticket_id', 'sender_id', 'message'], properties: [
    new OA\Property(property: 'ticket_id', type: 'integer', minimum: 1, maximum: 2147483647, example: 1),
    new OA\Property(property: 'sender_id', type: 'integer', minimum: 1, maximum: 2147483647, example: 5),
    new OA\Property(property: 'message', type: 'string', maxLength: 10000, minLength: 1, example: 'Your appointment is confirmed.')
])]
#[OA\Schema(schema: 'TicketMessageUpdate', type: 'object', description: 'A customer may only reply to their own ticket; staff may reply to any open conversation. Resolved/Closed tickets reject replies. Only the latest reply can be edited or deleted; the opening message is preserved. ticket_id and sender_id cannot change. These relationship checks do not authenticate the caller (M4).', additionalProperties: false, minProperties: 1, properties: [
    new OA\Property(property: 'ticket_id', type: 'integer', minimum: 1, maximum: 2147483647, example: 1),
    new OA\Property(property: 'sender_id', type: 'integer', minimum: 1, maximum: 2147483647, example: 5),
    new OA\Property(property: 'message', type: 'string', maxLength: 10000, minLength: 1, example: 'Your appointment is confirmed.')
])]
#[OA\Schema(schema: 'TicketMessage', type: 'object', description: 'A customer may only reply to their own ticket; staff may reply to any open conversation. Resolved/Closed tickets reject replies. Only the latest reply can be edited or deleted; the opening message is preserved. ticket_id and sender_id cannot change. These relationship checks do not authenticate the caller (M4).', additionalProperties: false, required: ['ticket_id', 'sender_id', 'message', 'id', 'created_at'], properties: [
    new OA\Property(property: 'id', type: 'integer', minimum: 1, example: 1),
    new OA\Property(property: 'ticket_id', type: 'integer', minimum: 1, maximum: 2147483647, example: 1),
    new OA\Property(property: 'sender_id', type: 'integer', minimum: 1, maximum: 2147483647, example: 5),
    new OA\Property(property: 'message', type: 'string', maxLength: 10000, minLength: 1, example: 'Your appointment is confirmed.'),
    new OA\Property(property: 'created_at', type: 'string', example: '2026-11-22 12:00:00', description: 'Database timestamp, YYYY-MM-DD HH:MM:SS.')
])]
class OpenApiSpec {}
