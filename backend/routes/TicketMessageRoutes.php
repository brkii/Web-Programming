<?php
declare(strict_types=1);

Flight::route('GET /ticket-messages', static function (): void {
    Flight::json(Flight::ticketMessageService()->getAll());
});
Flight::route('GET /ticket-messages/@id:[0-9]+', static function ($id): void {
    Flight::json(Flight::ticketMessageService()->getById($id));
});
Flight::route('POST /ticket-messages', static function (): void {
    Flight::json(Flight::ticketMessageService()->create(HttpInput::json()), 201);
});
Flight::route('PUT /ticket-messages/@id:[0-9]+', static function ($id): void {
    Flight::json(Flight::ticketMessageService()->update($id, HttpInput::json()));
});
Flight::route('DELETE /ticket-messages/@id:[0-9]+', static function ($id): void {
    Flight::ticketMessageService()->delete($id);
    Flight::json(['message' => 'Record deleted.']);
});
