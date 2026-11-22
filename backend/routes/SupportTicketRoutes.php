<?php
declare(strict_types=1);

Flight::route('GET /support-tickets', static function (): void {
    Flight::json(Flight::supportTicketService()->getAll());
});
Flight::route('GET /support-tickets/@id:[0-9]+', static function ($id): void {
    Flight::json(Flight::supportTicketService()->getById($id));
});
Flight::route('POST /support-tickets', static function (): void {
    Flight::json(Flight::supportTicketService()->create(HttpInput::json()), 201);
});
Flight::route('PUT /support-tickets/@id:[0-9]+', static function ($id): void {
    Flight::json(Flight::supportTicketService()->update($id, HttpInput::json()));
});
Flight::route('DELETE /support-tickets/@id:[0-9]+', static function ($id): void {
    Flight::supportTicketService()->delete($id);
    Flight::json(['message' => 'Record deleted.']);
});
