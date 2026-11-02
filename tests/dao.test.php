<?php
declare(strict_types=1);

// CLI only. Run on an imported test database. Rows are rolled back;
// MySQL's AUTO_INCREMENT counters may still advance.
if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}
foreach (glob(__DIR__ . '/../backend/dao/*Dao.php') as $file) {
    require_once $file;
}

function check(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

function rejects(callable $operation, string $message): void
{
    try {
        $operation();
    } catch (PDOException $error) {
        // Integrity/check errors only, never count connection or syntax failures as a pass.
        if (in_array((int) ($error->errorInfo[1] ?? 0), [1062, 1451, 1452, 3819, 4025], true)) {
            return;
        }
        throw $error;
    }
    throw new RuntimeException($message);
}

$daos = [
    'users' => new UserDao(), 'vehicles' => new VehicleDao(),
    'services' => new ServiceDao(), 'parts' => new PartDao(),
    'requests' => new ServiceRequestDao(), 'orders' => new ServiceOrderDao(),
    'items' => new ServiceOrderItemDao(), 'tickets' => new SupportTicketDao(),
    'messages' => new TicketMessageDao(),
];
$users = $daos['users'];
try {
    $counts = array_map(fn ($dao) => $dao->countAll(), $daos);
    $users->beginTransaction();
    $suffix = bin2hex(random_bytes(6));
    $hash = password_hash('Local test password', PASSWORD_BCRYPT);
    $rows = [];
    $rows['users'] = $users->insert(['full_name' => 'Test Čćžšđ', 'email' => "test-$suffix@example.test", 'password_hash' => $hash, 'role' => 'customer', 'city' => 'Sarajevo', 'id' => -1, 'created_at' => '2000-01-01', 'hacker` = 1 --' => 'ignored']);
    $userId = (int) $rows['users']['id'];
    $rows['vehicles'] = $daos['vehicles']->insert(['user_id' => $userId, 'vin' => '', 'make' => 'Test', 'model' => 'Coupe', 'year' => 2022, 'license_plate' => "T-$suffix", 'mileage' => 100, 'fuel_type' => 'Petrol', 'transmission' => 'Manual']);
    $rows['services'] = $daos['services']->insert(['name' => "Test $suffix", 'description' => "Quote ' OR 1=1 --", 'standard_price' => '19.95', 'estimated_duration' => 30]);
    $rows['parts'] = $daos['parts']->insert(['name' => 'Test part', 'sku' => "T-$suffix", 'category' => 'Filters', 'quantity' => 3, 'unit_price' => '4.25', 'minimum_stock' => 2]);
    $rows['requests'] = $daos['requests']->insert(['vehicle_id' => $rows['vehicles']['id'], 'requested_by' => $userId, 'service_id' => $rows['services']['id'], 'description' => 'Test request', 'preferred_date' => '2026-11-10']);
    $rows['orders'] = $daos['orders']->insert(['service_request_id' => $rows['requests']['id'], 'mechanic_id' => null, 'diagnosis' => 'Pending', 'estimated_cost' => '39.90']);
    $rows['items'] = $daos['items']->insert(['service_order_id' => $rows['orders']['id'], 'service_id' => $rows['services']['id'], 'part_id' => null, 'quantity' => 2, 'unit_price' => '19.95']);
    $rows['tickets'] = $daos['tickets']->insert(['customer_id' => $userId, 'service_request_id' => $rows['requests']['id'], 'subject' => 'Question', 'description' => 'Test ticket', 'category' => 'Service']);
    $rows['messages'] = $daos['messages']->insert(['ticket_id' => $rows['tickets']['id'], 'sender_id' => $userId, 'message' => 'Hello']);

    $updates = ['users' => ['city' => 'Mostar'], 'vehicles' => ['mileage' => 150],
        'services' => ['name' => 'Updated service'], 'parts' => ['category' => 'Updated'],
        'requests' => ['notes' => 'Updated'], 'orders' => ['diagnosis' => 'Updated'],
        'items' => ['quantity' => 3], 'tickets' => ['subject' => 'Updated'], 'messages' => ['message' => 'Updated']];
    foreach ($daos as $name => $dao) {
        $id = (int) $rows[$name]['id'];
        check($dao->countAll() === $counts[$name] + 1, "$name count after insert");
        check(in_array($id, array_column($dao->getAll(), 'id')), "$name getAll");
        check($dao->update($id, $updates[$name] + ['id' => -1, 'unknown' => 'ignored']) === 1, "$name update");
        $updated = $dao->getById($id);
        foreach ($updates[$name] as $column => $value) {
            check((string) $updated[$column] === (string) $value, "$name read updated value");
        }
        check($dao->update($id, $updates[$name]) === 0, "$name unchanged update");
        check($dao->getById(-1) === null && $dao->delete(-1) === 0, "$name missing row");
        check($dao->update($id, []) === 0, "$name empty update");
    }
    echo "PASS: create, read, update and missing-row behavior for all nine DAOs\n";

    check(!isset($rows['users']['password_hash']), 'Insert must not return password hash');
    check($rows['users']['full_name'] === 'Test Čćžšđ', 'utf8mb4 text');
    check($rows['vehicles']['vin'] === null, 'Blank VIN normalized to NULL');
    check($rows['services']['description'] === "Quote ' OR 1=1 --", 'Injection text stored literally');
    check($users->getByEmail("' OR 1=1 --") === null, 'Injection must not find a user');
    check(!isset($users->getById($userId)['password_hash']), 'getById hides hash');
    check(!isset($users->getByEmail("test-$suffix@example.test")['password_hash']), 'getByEmail hides hash');
    foreach ($users->getAll() as $user) {
        check(!isset($user['password_hash']), 'getAll hides hash');
    }
    check(password_verify('Local test password', $users->getCredentialsByEmail("test-$suffix@example.test")['password_hash']), 'Explicit credentials lookup');
    check($users->update($userId, ['id' => -1, 'hacker' => 'value']) === 0, 'Writable-column whitelist');
    try {
        $users->insert(['hacker' => 'value']);
        throw new RuntimeException('Empty insert should fail');
    } catch (InvalidArgumentException $expected) {
        // No SQL should run when no writable keys remain.
    }
    echo "PASS: UTF-8, blank VIN, SQL injection, column whitelist and hash privacy\n";

    check(count($daos['vehicles']->getByOwner($userId)) === 1, 'Owner filter');
    check(count($daos['requests']->getByCustomer($userId)) === 1, 'Request JOIN');
    check($daos['orders']->getByRequest((int) $rows['requests']['id'])['id'] === $rows['orders']['id'], 'Request/order lookup');
    check($daos['orders']->getByMechanic(-1) === [], 'Mechanic filter');
    check($daos['items']->getByOrder((int) $rows['orders']['id'])[0]['item_name'] === 'Updated service', 'Item JOIN');
    check($daos['items']->getTotalByOrder((int) $rows['orders']['id']) === '59.85', 'Exact decimal total');
    check(count($daos['tickets']->getByCustomer($userId)) === 1, 'Ticket customer filter');
    check($daos['messages']->getByTicket((int) $rows['tickets']['id'])[0]['sender_name'] === 'Test Čćžšđ', 'Message JOIN');
    $partId = (int) $rows['parts']['id'];
    check($daos['parts']->changeStock($partId, -4) === 0, 'Insufficient stock refused');
    check($daos['parts']->changeStock($partId, -1) === 1, 'Atomic stock decrement');
    check(in_array($partId, array_column($daos['parts']->getLowStock(), 'id')), 'Low stock query');
    $unused = $daos['services']->insert(['name' => 'No requests', 'description' => '', 'standard_price' => '0.00', 'estimated_duration' => 1]);
    $statistics = array_column($daos['requests']->countByService(), 'total', 'id');
    check((int) $statistics[$unused['id']] === 0, 'LEFT JOIN includes unused services');
    $daos['services']->delete((int) $unused['id']);
    echo "PASS: filters, JOINs, GROUP BY, exact prices and atomic stock change\n";

    rejects(fn () => $users->update($userId, ['email' => 'amina@example.test']), 'Duplicate email should fail');
    rejects(fn () => $daos['vehicles']->update((int) $rows['vehicles']['id'], ['user_id' => -1]), 'Foreign key should fail');
    rejects(fn () => $daos['parts']->update($partId, ['quantity' => -1]), 'Negative stock should fail');
    rejects(fn () => $daos['items']->update((int) $rows['items']['id'], ['part_id' => $partId]), 'Both item sources should fail');
    rejects(fn () => $daos['items']->update((int) $rows['items']['id'], ['service_id' => null]), 'Missing item source should fail');
    rejects(fn () => $daos['orders']->insert(['service_request_id' => $rows['requests']['id']]), 'Second order should fail');
    rejects(fn () => $users->delete($userId), 'Referenced customer delete should fail');
    echo "PASS: unique, foreign key, CHECK and historical deletion constraints\n";

    foreach (['messages', 'tickets', 'items', 'orders', 'requests', 'vehicles', 'parts', 'services', 'users'] as $name) {
        $id = (int) $rows[$name]['id'];
        check($daos[$name]->delete($id) === 1, "$name delete");
        check($daos[$name]->getById($id) === null, "$name deleted row");
    }
    $temporary = $users->insert(['full_name' => 'Rollback', 'email' => "rollback-$suffix@example.test", 'password_hash' => $hash, 'city' => 'Sarajevo']);
    $users->rollback();
    check($users->getById((int) $temporary['id']) === null, 'Rollback removes uncommitted row');
    foreach ($daos as $name => $dao) {
        check($dao->countAll() === $counts[$name], "$name original rows preserved");
    }
    // Exercise commit without leaving sample changes behind.
    $users->beginTransaction();
    $users->commit();
    check(!BaseDao::connection()->inTransaction(), 'Commit closes transaction');
    echo "PASS: delete for all nine DAOs and shared transaction rollback/commit\n";
} catch (Throwable $error) {
    try {
        $users->rollback();
    } catch (Throwable $rollbackError) {
        // Preserve the original error, including a failed database connection.
    }
    fwrite(STDERR, 'FAIL: ' . $error->getMessage() . PHP_EOL);
    exit(1);
}
