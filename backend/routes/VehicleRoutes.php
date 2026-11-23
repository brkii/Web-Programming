<?php
declare(strict_types=1);

Flight::route('GET /vehicles', static function (): void {
    Flight::json(Flight::vehicleService()->getAll());
});
Flight::route('GET /vehicles/@id:[0-9]+', static function ($id): void {
    Flight::json(Flight::vehicleService()->getById($id));
});
Flight::route('POST /vehicles', static function (): void {
    Flight::json(Flight::vehicleService()->create(HttpInput::json()), 201);
});
Flight::route('PUT /vehicles/@id:[0-9]+', static function ($id): void {
    Flight::json(Flight::vehicleService()->update($id, HttpInput::json()));
});
Flight::route('DELETE /vehicles/@id:[0-9]+', static function ($id): void {
    Flight::vehicleService()->delete($id);
    Flight::json(['message' => 'Record deleted.']);
});
