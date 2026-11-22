<?php
declare(strict_types=1);

Flight::route('GET /parts', static function (): void {
    Flight::json(Flight::partService()->getAll());
});
Flight::route('GET /parts/@id:[0-9]+', static function ($id): void {
    Flight::json(Flight::partService()->getById($id));
});
Flight::route('POST /parts', static function (): void {
    Flight::json(Flight::partService()->create(HttpInput::json()), 201);
});
Flight::route('PUT /parts/@id:[0-9]+', static function ($id): void {
    Flight::json(Flight::partService()->update($id, HttpInput::json()));
});
Flight::route('DELETE /parts/@id:[0-9]+', static function ($id): void {
    Flight::partService()->delete($id);
    Flight::json(['message' => 'Record deleted.']);
});
