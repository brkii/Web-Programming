<?php
declare(strict_types=1);

Flight::route('GET /service-order-items', static function (): void {
    Flight::json(Flight::serviceOrderItemService()->getAll());
});
Flight::route('GET /service-order-items/@id:[0-9]+', static function ($id): void {
    Flight::json(Flight::serviceOrderItemService()->getById($id));
});
Flight::route('POST /service-order-items', static function (): void {
    Flight::json(Flight::serviceOrderItemService()->create(HttpInput::json()), 201);
});
Flight::route('PUT /service-order-items/@id:[0-9]+', static function ($id): void {
    Flight::json(Flight::serviceOrderItemService()->update($id, HttpInput::json()));
});
Flight::route('DELETE /service-order-items/@id:[0-9]+', static function ($id): void {
    Flight::serviceOrderItemService()->delete($id);
    Flight::json(['message' => 'Record deleted.']);
});
