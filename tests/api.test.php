<?php
declare(strict_types=1);
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }

// Run against a local API configured with a separate, imported test database.
$base = rtrim($argv[1] ?? 'http://127.0.0.1:8000/backend', '/');
if (!in_array(parse_url($base, PHP_URL_HOST), ['127.0.0.1', 'localhost', '::1'], true)) { exit("Use a local test API.\n"); }
$checks = 0;
$cleanup = [];
function request(string $method, string $path, mixed $body = null, int $expected = 200, bool $raw = false): mixed
{
    global $base, $checks;
    $curl = curl_init($base . $path);
    curl_setopt_array($curl, [CURLOPT_CUSTOMREQUEST => $method, CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 15]);
    if ($body !== null) {
        curl_setopt($curl, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $raw ? $body : json_encode($body));
    }
    $response = curl_exec($curl);
    $status = curl_getinfo($curl, CURLINFO_RESPONSE_CODE);
    $type = curl_getinfo($curl, CURLINFO_CONTENT_TYPE);
    $error = curl_error($curl);
    curl_close($curl);
    if ($response === false) { throw new RuntimeException($error); }
    if ($status !== $expected || !str_starts_with($type ?? '', 'application/json')) {
        throw new RuntimeException("$method $path: expected $expected JSON, received $status: $response");
    }
    $json = json_decode($response, true, 64, JSON_THROW_ON_ERROR);
    if ($status >= 400 && (!isset($json['error']) || count($json) !== 1)) { throw new RuntimeException('Unexpected error envelope.'); }
    if (str_contains($response, 'password_hash') || str_contains($response, 'Stack trace')) { throw new RuntimeException('Private details leaked.'); }
    $checks++;
    return $json;
}
function create(string $resource, array $data): array
{
    global $cleanup;
    $row = request('POST', '/' . $resource, $data, 201);
    $cleanup[] = [$resource, $row['id']];
    return $row;
}
function verify(bool $ok, string $message): void
{
    global $checks;
    if (!$ok) { throw new RuntimeException($message); }
    $checks++;
}

try {
    $spec = request('GET', '/public/v1/docs/swagger.php');
    verify($spec['openapi'] === '3.0.3' && count($spec['components']['schemas']) === 29, 'Incomplete OpenAPI.');
    verify($spec['servers'][0]['url'] === rtrim(parse_url($base, PHP_URL_PATH), '/') . '/', 'Wrong Swagger base URL.');
    $schemaNames = array_keys($spec['components']['schemas']);
    $walk = function (array $node) use (&$walk, $schemaNames): void {
        if (isset($node['$ref'])) { verify(in_array(basename($node['$ref']), $schemaNames, true), 'Unresolved OpenAPI reference.'); }
        foreach ($node as $child) { if (is_array($child)) { $walk($child); } }
    };
    $walk($spec);
    request('GET', '/');
    foreach (['users', 'vehicles', 'services', 'parts', 'service-requests', 'service-orders', 'service-order-items', 'support-tickets', 'ticket-messages'] as $resource) {
        request('GET', '/' . $resource);
        request('GET', '/' . $resource . '/2147483647', null, 404);
        request('PUT', '/' . $resource . '/2147483647', ['unknown' => 1], 400);
        request('DELETE', '/' . $resource . '/2147483647', null, 404);
        request('POST', '/' . $resource, '{}', 400, true);
        foreach (['get', 'post'] as $method) { verify(isset($spec['paths']['/' . $resource][$method]), 'Missing collection operation.'); }
        foreach (['get', 'put', 'delete'] as $method) { verify(isset($spec['paths']['/' . $resource . '/{id}'][$method]), 'Missing item operation.'); }
    }
    request('GET', '/unknown', null, 404);
    request('GET', '/users/abc', null, 404);
    request('GET', '/users/0', null, 400);
    request('POST', '/users', '{', 400, true);
    request('POST', '/users', '[]', 400, true);
    request('POST', '/users', ['full_name' => ['nested']], 400);
    request('GET', '/vin/invalid', null, 400);
    request('GET', '/vin/1HGCM82633A004352?model_year=bad', null, 400);

    $suffix = bin2hex(random_bytes(5));
    $user = create('users', ['full_name' => 'HTTP Customer', 'email' => "http-$suffix@example.test", 'password' => 'Secure123!', 'city' => 'Sarajevo']);
    $vehicle = create('vehicles', ['user_id' => $user['id'], 'make' => 'Test', 'model' => 'Car', 'year' => 2022, 'license_plate' => $suffix, 'fuel_type' => 'Petrol', 'transmission' => 'Manual']);
    $service = create('services', ['name' => 'HTTP labor', 'description' => 'Test labor', 'standard_price' => '12.25', 'estimated_duration' => 30]);
    $part = create('parts', ['name' => 'HTTP part', 'sku' => $suffix, 'category' => 'Test', 'quantity' => 5, 'unit_price' => '2.50']);
    $serviceRequest = create('service-requests', ['vehicle_id' => $vehicle['id'], 'requested_by' => $user['id'], 'service_id' => $service['id'], 'description' => 'HTTP repair', 'preferred_date' => '2026-12-01']);
    $order = create('service-orders', ['service_request_id' => $serviceRequest['id']]);
    $item = create('service-order-items', ['service_order_id' => $order['id'], 'part_id' => $part['id'], 'quantity' => 1]);
    $ticket = create('support-tickets', ['customer_id' => $user['id'], 'service_request_id' => $serviceRequest['id'], 'subject' => 'HTTP ticket', 'description' => 'Opening question', 'category' => 'Test']);
    $reply = create('ticket-messages', ['ticket_id' => $ticket['id'], 'sender_id' => $user['id'], 'message' => 'Additional detail']);
    $edits = [
        ['users', $user, ['city' => 'Mostar'], 'User'],
        ['vehicles', $vehicle, ['mileage' => 1000], 'Vehicle'],
        ['services', $service, ['standard_price' => '20.00'], 'Service'],
        ['parts', $part, ['unit_price' => '5.00'], 'Part'],
        ['service-requests', $serviceRequest, ['notes' => 'Updated note'], 'ServiceRequest'],
        ['service-orders', $order, ['diagnosis' => 'Inspection pending'], 'ServiceOrder'],
        ['service-order-items', $item, ['quantity' => 2], 'ServiceOrderItem'],
        ['support-tickets', $ticket, ['priority' => 'High'], 'SupportTicket'],
        ['ticket-messages', $reply, ['message' => 'Updated detail'], 'TicketMessage'],
    ];
    foreach ($edits as [$resource, $row, $data, $schemaName]) {
        $updated = request('PUT', '/' . $resource . '/' . $row['id'], $data);
        foreach ($data as $key => $value) { verify($updated[$key] === $value, "$resource update did not persist."); }
        $read = request('GET', '/' . $resource . '/' . $row['id']);
        $schema = $spec['components']['schemas'][$schemaName];
        verify(array_diff($schema['required'], array_keys($read)) === [], "$schemaName missing required output fields.");
        verify(array_diff(array_keys($read), array_keys($schema['properties'])) === [], "$schemaName undocumented output fields.");
    }
    $snapshot = request('GET', '/service-order-items/' . $item['id']);
    verify($snapshot['unit_price'] === '2.50', 'HTTP update lost price snapshot.');
    request('PUT', '/service-orders/' . $order['id'], ['final_cost' => '0.01'], 400);
    request('DELETE', '/users/' . $user['id'], null, 400);
} catch (Throwable $e) {
    fwrite(STDERR, 'FAIL: ' . $e->getMessage() . "\n");
    $failed = true;
} finally {
    foreach (array_reverse($cleanup) as [$resource, $id]) {
        try { request('DELETE', '/' . $resource . '/' . $id); request('GET', '/' . $resource . '/' . $id, null, 404); }
        catch (Throwable $e) { fwrite(STDERR, "Cleanup failed for $resource/$id: {$e->getMessage()}\n"); $failed = true; }
    }
}
if (!isset($failed)) { echo "PASS: $checks HTTP/OpenAPI checks, all nine CRUD resources; fixtures deleted.\n"; }
exit(isset($failed) ? 1 : 0);
