<?php
declare(strict_types=1);

// Router for: php -S localhost:8000 server.php (run from the project root).
$path = rawurldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '/');
$file = realpath(__DIR__ . $path);
$frontend = realpath(__DIR__ . '/frontend');
if ($path === '/') { header('Location: /frontend/'); return; }
if ($file && ($file === $frontend || str_starts_with($file, $frontend . DIRECTORY_SEPARATOR))) {
    return false;
}
$docs = '/backend/public/v1/docs';
if ($path === $docs) { header('Location: ' . $docs . '/'); return; }
if (in_array($path, [$docs . '/', $docs . '/index.html'], true)) {
    header('Content-Type: text/html; charset=utf-8');
    readfile(__DIR__ . $docs . '/index.html');
    return;
}
if ($path === $docs . '/swagger.php') {
    $_SERVER['SCRIPT_NAME'] = $path;
    require __DIR__ . $path;
    return;
}
if ($path === '/backend' || str_starts_with($path, '/backend/')) {
    $_SERVER['SCRIPT_NAME'] = '/backend/index.php';
    require __DIR__ . '/backend/index.php';
    return;
}
http_response_code(404);
header('Content-Type: application/json; charset=utf-8');
echo json_encode(['error' => 'Endpoint not found.']);
