<?php
declare(strict_types=1);

ini_set('display_errors', '0');
ini_set('log_errors', '1');
ini_set('error_log', dirname(__DIR__, 3) . '/logs/error.log');
require dirname(__DIR__, 3) . '/vendor/autoload.php';

// Same-origin relative URL works at /backend and /project-name/backend in XAMPP.
define('API_BASE_URL', rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/backend/public/v1/docs/swagger.php', 4)), '/') . '/');
require_once __DIR__ . '/doc_setup.php';
foreach (glob(__DIR__ . '/paths/*.php') as $file) { require_once $file; }
header('Content-Type: application/json; charset=utf-8');
try {
    $document = (new OpenApi\Generator())->setVersion('3.0.3')->generate([__DIR__ . '/doc_setup.php', __DIR__ . '/paths']);
    if ($document === null || !$document->validate()) { throw new RuntimeException('OpenAPI validation failed.'); }
    echo $document->toJson();
} catch (Throwable $e) {
    error_log((string) $e);
    http_response_code(500);
    echo json_encode(['error' => 'API documentation could not be generated.']);
}
