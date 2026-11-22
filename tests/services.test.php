<?php
declare(strict_types=1);
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
require __DIR__ . '/../backend/vendor/autoload.php';

$checks = 0;
function check(bool $ok, string $message): void
{
    global $checks;
    if (!$ok) { throw new RuntimeException($message); }
    $checks++;
}
function fails(callable $operation, int $status = 400): void
{
    try { $operation(); } catch (HttpException $e) { check($e->getStatus() === $status, 'Wrong error status: ' . $e->getMessage()); return; }
    throw new RuntimeException('Expected an exception.');
}

$dao = new UserDao();
$dao->beginTransaction();
try {
    $suffix = bin2hex(random_bytes(5));
    $users = new UserService();
    $customer = $users->create(['full_name' => 'Test Customer', 'email' => "M3-$suffix@example.test", 'password' => 'Secure123!', 'city' => 'Sarajevo']);
    $mechanic = $users->create(['full_name' => 'Test Mechanic', 'email' => "mechanic-$suffix@example.test", 'password' => 'Secure123!', 'city' => 'Sarajevo', 'role' => 'mechanic']);
    check(!isset($customer['password_hash']) && !isset($customer['password']), 'Password leaked.');
    check(password_verify('Secure123!', $dao->getCredentialsByEmail($customer['email'])['password_hash']), 'Password not hashed.');
    check($customer['email'] === strtolower($customer['email']), 'Email not normalized.');
    fails(fn() => $users->create(['full_name' => 'Duplicate', 'email' => $customer['email'], 'password' => 'Secure123!', 'city' => 'Sarajevo']));
    fails(fn() => $users->update($customer['id'], ['password_hash' => 'raw']));
    fails(fn() => $users->update($customer['id'], ['password' => 'weak']));
    check($users->update($customer['id'], ['city' => 'Sarajevo'])['city'] === 'Sarajevo', 'Unchanged update failed.');
    fails(fn() => $users->getById(2147483647), 404);
    fails(fn() => $users->update($customer['id'], []));

    $vehicles = new VehicleService();
    $vehicleData = ['user_id' => $customer['id'], 'make' => 'Test', 'model' => 'Car', 'year' => 2022, 'license_plate' => $suffix, 'fuel_type' => 'Petrol', 'transmission' => 'Manual'];
    fails(fn() => $vehicles->create(array_replace($vehicleData, ['user_id' => $mechanic['id']])));
    $vehicle = $vehicles->create($vehicleData);
    check($vehicle['vin'] === null, 'Optional VIN not null.');
    fails(fn() => $vehicles->update($vehicle['id'], ['vin' => 'I1234567890123456']));
    fails(fn() => $vehicles->update($vehicle['id'], ['year' => true]));
    fails(fn() => $vehicles->update($vehicle['id'], ['mileage' => -1]));
    $vehicles->update($vehicle['id'], ['model' => 'Updated']);

    $services = new ServiceService();
    $service = $services->create(['name' => 'Test labor', 'description' => 'Labor', 'standard_price' => '10.10', 'estimated_duration' => 20]);
    fails(fn() => $services->update($service['id'], ['standard_price' => '0.001']));
    $parts = new PartService();
    $part = $parts->create(['name' => 'Test part', 'sku' => $suffix, 'category' => 'Test', 'quantity' => 5, 'unit_price' => '2.35']);
    $requests = new ServiceRequestService();
    $requestData = ['vehicle_id' => $vehicle['id'], 'requested_by' => $customer['id'], 'service_id' => $service['id'], 'description' => 'Repair', 'preferred_date' => '2026-12-01'];
    fails(fn() => $requests->create(array_replace($requestData, ['requested_by' => $mechanic['id']])));
    fails(fn() => $requests->create(array_replace($requestData, ['preferred_date' => '2026-02-30'])));
    $request = $requests->create($requestData);
    fails(fn() => $users->update($customer['id'], ['role' => 'admin']));
    fails(fn() => $users->delete($customer['id']));
    fails(fn() => $services->delete($service['id']));

    $orders = new ServiceOrderService();
    $items = new ServiceOrderItemService();
    $itemDao = new ServiceOrderItemDao();
    fails(fn() => $orders->create(['service_request_id' => $request['id'], 'mechanic_id' => $customer['id']]));
    check($requests->getById($request['id'])['status'] === 'Requested', 'Failed order changed request.');
    $order = $orders->create(['service_request_id' => $request['id'], 'mechanic_id' => $mechanic['id'], 'estimated_cost' => '20.00']);
    check($requests->getById($request['id'])['status'] === 'Approved', 'Order did not synchronize request.');
    check(count($itemDao->getByOrder($order['id'])) === 1, 'Order missing initial item.');
    fails(fn() => $orders->create(['service_request_id' => $request['id']]));
    fails(fn() => $requests->update($request['id'], ['status' => 'Completed']));
    $labor = $itemDao->getByOrder($order['id'])[0];
    fails(fn() => $items->delete($labor['id']));
    fails(fn() => $items->create(['service_order_id' => $order['id'], 'quantity' => 1]));
    fails(fn() => $items->create(['service_order_id' => $order['id'], 'service_id' => $service['id'], 'part_id' => $part['id'], 'quantity' => 1]));
    fails(fn() => $items->create(['service_order_id' => $order['id'], 'part_id' => $part['id'], 'quantity' => 6]));
    check($parts->getById($part['id'])['quantity'] === 5, 'Failed reservation changed stock.');
    $material = $items->create(['service_order_id' => $order['id'], 'part_id' => $part['id'], 'quantity' => 2]);
    check($parts->getById($part['id'])['quantity'] === 3, 'Stock not reserved.');
    $parts->update($part['id'], ['unit_price' => '99.00']);
    $services->update($service['id'], ['standard_price' => '50.00']);
    $items->update($material['id'], ['quantity' => 3]);
    check($items->getById($material['id'])['unit_price'] === '2.35', 'Snapshot changed.');
    check($parts->getById($part['id'])['quantity'] === 2, 'Quantity delta incorrect.');
    fails(fn() => $items->update($material['id'], ['unit_price' => '0.01']));
    fails(fn() => $items->update($material['id'], ['quantity' => 10]));
    check($items->getById($material['id'])['quantity'] === 3 && $parts->getById($part['id'])['quantity'] === 2, 'Failed update was not rolled back.');
    $extra = $items->create(['service_order_id' => $order['id'], 'part_id' => $part['id'], 'quantity' => 1]);
    $items->delete($extra['id']);
    check($parts->getById($part['id'])['quantity'] === 2, 'Deletion did not restore stock.');
    fails(fn() => $orders->update($order['id'], ['status' => 'Completed']));
    $orders->update($order['id'], ['status' => 'In Progress']);
    $orders->update($order['id'], ['status' => 'Ready for Pickup']);
    fails(fn() => $items->update($material['id'], ['quantity' => 1]));
    $completed = $orders->update($order['id'], ['status' => 'Completed']);
    check($completed['final_cost'] === '17.15', 'Final cost must use original 10.10 + 3 * 2.35.');
    check($requests->getById($request['id'])['status'] === 'Completed', 'Completion not synchronized.');
    fails(fn() => $orders->update($order['id'], ['status' => 'In Progress']));
    fails(fn() => $orders->delete($order['id']));

    $pendingRequest = $requests->create($requestData);
    $pendingOrder = $orders->create(['service_request_id' => $pendingRequest['id']]);
    $items->create(['service_order_id' => $pendingOrder['id'], 'part_id' => $part['id'], 'quantity' => 1]);
    $orders->delete($pendingOrder['id']);
    check($parts->getById($part['id'])['quantity'] === 2, 'Deleting draft order did not release stock.');
    check($requests->getById($pendingRequest['id'])['status'] === 'Requested', 'Deleting order did not reset request.');
    $requests->delete($pendingRequest['id']);
    $cancelRequest = $requests->create($requestData);
    $cancelOrder = $orders->create(['service_request_id' => $cancelRequest['id']]);
    $items->create(['service_order_id' => $cancelOrder['id'], 'part_id' => $part['id'], 'quantity' => 2]);
    $parts->update($part['id'], ['quantity' => 2147483647]);
    fails(fn() => $orders->update($cancelOrder['id'], ['status' => 'Cancelled']));
    check($orders->getById($cancelOrder['id'])['status'] === 'Approved', 'Failed stock return changed order status.');
    $parts->update($part['id'], ['quantity' => 0]);
    $orders->update($cancelOrder['id'], ['status' => 'Cancelled']);
    $orders->update($cancelOrder['id'], ['status' => 'Cancelled']);
    check($parts->getById($part['id'])['quantity'] === 2, 'Repeated cancellation returned stock twice.');

    $tickets = new SupportTicketService();
    $messages = new TicketMessageService();
    $ticketData = ['customer_id' => $customer['id'], 'service_request_id' => $request['id'], 'subject' => 'Question', 'description' => 'Opening message', 'category' => 'Repair'];
    $ticket = $tickets->create($ticketData);
    $opening = (new TicketMessageDao())->getByTicket($ticket['id']);
    check(count($opening) === 1 && $opening[0]['message'] === 'Opening message', 'Ticket missing opening message.');
    fails(fn() => $messages->delete($opening[0]['id']));
    $reply = $messages->create(['ticket_id' => $ticket['id'], 'sender_id' => $mechanic['id'], 'message' => 'Reply']);
    check($messages->update($reply['id'], ['message' => 'Updated reply'])['message'] === 'Updated reply', 'Reply update failed.');
    fails(fn() => $tickets->delete($ticket['id']));
    $messages->delete($reply['id']);
    $tickets->update($ticket['id'], ['status' => 'In Progress']);
    $tickets->update($ticket['id'], ['status' => 'Resolved']);
    fails(fn() => $messages->create(['ticket_id' => $ticket['id'], 'sender_id' => $mechanic['id'], 'message' => 'Late']));
    $tickets->update($ticket['id'], ['status' => 'Closed']);
    fails(fn() => $tickets->update($ticket['id'], ['status' => 'Open']));
    $unanswered = $tickets->create($ticketData);
    $tickets->delete($unanswered['id']);
    fails(fn() => $tickets->getById($unanswered['id']), 404);

    $unusedPart = $parts->create(['name' => 'Unused', 'sku' => 'unused-' . $suffix, 'category' => 'Test', 'unit_price' => '0.00']);
    $parts->delete($unusedPart['id']);
    $unusedService = $services->create(['name' => 'Unused', 'description' => 'Test', 'standard_price' => 0, 'estimated_duration' => 1]);
    $services->delete($unusedService['id']);
    $unusedVehicle = $vehicles->create(array_replace($vehicleData, ['license_plate' => 'unused-' . $suffix]));
    $vehicles->delete($unusedVehicle['id']);
    $unusedUser = $users->create(['full_name' => 'Unused', 'email' => "unused-$suffix@example.test", 'password' => 'Secure123!', 'city' => 'Mostar']);
    $users->delete($unusedUser['id']);
    echo "PASS: $checks service checks; all fixtures rolled back.\n";
} catch (Throwable $e) {
    fwrite(STDERR, "FAIL: {$e->getMessage()}\n{$e->getTraceAsString()}\n");
    $failed = true;
} finally { $dao->rollback(); }
exit(isset($failed) ? 1 : 0);
