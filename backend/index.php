<?php
declare(strict_types=1);

ini_set('display_errors', '0');
ini_set('log_errors', '1');
ini_set('error_log', __DIR__ . '/logs/error.log');
require __DIR__ . '/vendor/autoload.php';

Flight::set('flight.log_errors', false);
Flight::map('error', static function (Throwable $e): void {
    $status = $e instanceof HttpException ? $e->getStatus() : 500;
    $message = $e instanceof HttpException ? $e->getMessage() : 'Something went wrong on the server. Please try again later.';
    if ($status === 500) { error_log((string) $e); }
    Flight::response()->clearBody()->status($status)->header('Content-Type', 'application/json; charset=utf-8')
        ->write(json_encode(['error' => $message], JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE))->send();
});
Flight::map('notFound', static function (): void {
    Flight::json(['error' => 'Endpoint not found.'], 404);
});

Flight::register('userService', UserService::class);
Flight::register('vehicleService', VehicleService::class);
Flight::register('serviceService', ServiceService::class);
Flight::register('partService', PartService::class);
Flight::register('serviceRequestService', ServiceRequestService::class);
Flight::register('serviceOrderService', ServiceOrderService::class);
Flight::register('serviceOrderItemService', ServiceOrderItemService::class);
Flight::register('supportTicketService', SupportTicketService::class);
Flight::register('ticketMessageService', TicketMessageService::class);
Flight::register('vinService', VinService::class);

Flight::route('GET /', static function (): void { Flight::json(['app' => 'PitStop OS API', 'milestone' => 3]); });
foreach (glob(__DIR__ . '/routes/*Routes.php') as $file) { require $file; }
Flight::start();
