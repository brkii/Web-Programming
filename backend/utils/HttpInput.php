<?php
declare(strict_types=1);

class HttpInput
{
    public static function json(): array
    {
        $contentType = strtolower(trim(explode(';', $_SERVER['CONTENT_TYPE'] ?? '')[0]));
        if ($contentType !== 'application/json') { throw new ValidationException('Send the body as application/json.'); }
        $body = file_get_contents('php://input', false, null, 0, 1048577);
        if ($body === false || strlen($body) > 1048576) { throw new ValidationException('The JSON body must not exceed 1 MB.'); }
        try { $object = json_decode($body, false, 32, JSON_THROW_ON_ERROR); }
        catch (JsonException $e) { throw new ValidationException('The request body contains invalid JSON.'); }
        if (!$object instanceof stdClass) { throw new ValidationException('The JSON body must be an object.'); }
        return (array) $object;
    }
}
