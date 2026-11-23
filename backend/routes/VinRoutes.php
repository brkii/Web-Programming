<?php
declare(strict_types=1);

Flight::route('GET /vin/@vin', static function ($vin): void {
    Flight::json(Flight::vinService()->decode($vin, Flight::request()->query['model_year']));
});
