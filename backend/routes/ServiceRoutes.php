<?php
declare(strict_types=1);

Flight::route('GET /services', static function (): void {
    Flight::json(Flight::serviceService()->getAll());
});
Flight::route('GET /services/@id:[0-9]+', static function ($id): void {
    Flight::json(Flight::serviceService()->getById($id));
});
Flight::route('POST /services', static function (): void {
    Flight::json(Flight::serviceService()->create(HttpInput::json()), 201);
});
Flight::route('PUT /services/@id:[0-9]+', static function ($id): void {
    Flight::json(Flight::serviceService()->update($id, HttpInput::json()));
});
Flight::route('DELETE /services/@id:[0-9]+', static function ($id): void {
    Flight::serviceService()->delete($id);
    Flight::json(['message' => 'Record deleted.']);
});
