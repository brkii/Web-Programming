<?php
declare(strict_types=1);

Flight::route('GET /service-requests', static function (): void {
    Flight::json(Flight::serviceRequestService()->getAll());
});
Flight::route('GET /service-requests/@id:[0-9]+', static function ($id): void {
    Flight::json(Flight::serviceRequestService()->getById($id));
});
Flight::route('POST /service-requests', static function (): void {
    Flight::json(Flight::serviceRequestService()->create(HttpInput::json()), 201);
});
Flight::route('PUT /service-requests/@id:[0-9]+', static function ($id): void {
    Flight::json(Flight::serviceRequestService()->update($id, HttpInput::json()));
});
Flight::route('DELETE /service-requests/@id:[0-9]+', static function ($id): void {
    Flight::serviceRequestService()->delete($id);
    Flight::json(['message' => 'Record deleted.']);
});
