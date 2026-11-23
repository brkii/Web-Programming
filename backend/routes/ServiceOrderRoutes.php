<?php
declare(strict_types=1);

Flight::route('GET /service-orders', static function (): void {
    Flight::json(Flight::serviceOrderService()->getAll());
});
Flight::route('GET /service-orders/@id:[0-9]+', static function ($id): void {
    Flight::json(Flight::serviceOrderService()->getById($id));
});
Flight::route('POST /service-orders', static function (): void {
    Flight::json(Flight::serviceOrderService()->create(HttpInput::json()), 201);
});
Flight::route('PUT /service-orders/@id:[0-9]+', static function ($id): void {
    Flight::json(Flight::serviceOrderService()->update($id, HttpInput::json()));
});
Flight::route('DELETE /service-orders/@id:[0-9]+', static function ($id): void {
    Flight::serviceOrderService()->delete($id);
    Flight::json(['message' => 'Record deleted.']);
});
