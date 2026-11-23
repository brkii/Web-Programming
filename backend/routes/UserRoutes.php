<?php
declare(strict_types=1);

Flight::route('GET /users', static function (): void {
    Flight::json(Flight::userService()->getAll());
});
Flight::route('GET /users/@id:[0-9]+', static function ($id): void {
    Flight::json(Flight::userService()->getById($id));
});
Flight::route('POST /users', static function (): void {
    Flight::json(Flight::userService()->create(HttpInput::json()), 201);
});
Flight::route('PUT /users/@id:[0-9]+', static function ($id): void {
    Flight::json(Flight::userService()->update($id, HttpInput::json()));
});
Flight::route('DELETE /users/@id:[0-9]+', static function ($id): void {
    Flight::userService()->delete($id);
    Flight::json(['message' => 'Record deleted.']);
});
