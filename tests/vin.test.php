<?php
declare(strict_types=1);
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
require __DIR__ . '/../backend/vendor/autoload.php';

class StubHttpClient extends JsonHttpClient
{
    public string $url = '';
    public function __construct(private array|Throwable $result) {}
    public function get(string $url): array
    {
        $this->url = $url;
        if ($this->result instanceof Throwable) { throw $this->result; }
        return $this->result;
    }
}
$checks = 0;
function verify(bool $ok): void { global $checks; if (!$ok) { throw new RuntimeException('VIN assertion failed.'); } $checks++; }
try {
    $client = new StubHttpClient(['Results' => [['ErrorCode' => '0', 'Make' => 'HONDA', 'Model' => 'Accord', 'ModelYear' => '2003', 'FuelTypePrimary' => 'Gasoline', 'BodyClass' => 'Coupe']]]);
    $service = new VinService($client);
    $row = $service->decode('1hgcm82633a004352', 2003);
    verify($row['vin'] === '1HGCM82633A004352' && $row['make'] === 'HONDA' && $row['year'] === 2003 && $row['complete']);
    verify($client->url === 'https://vpic.nhtsa.dot.gov/api/vehicles/DecodeVinValues/1HGCM82633A004352?format=json&modelyear=2003');
    foreach (['short', 'I1234567890123456', ['nested']] as $vin) {
        try { $service->decode($vin); throw new RuntimeException('Invalid VIN accepted.'); }
        catch (ValidationException $e) { verify($e->getStatus() === 400); }
    }
    $partial = (new VinService(new StubHttpClient(['Results' => [['ErrorCode' => '7', 'Make' => '', 'ModelYear' => '']]])))->decode('1HGCM82633A004352');
    verify(!$partial['complete'] && $partial['make'] === null && $partial['year'] === null);
    // Expected upstream errors are logged to a temporary file, not the test output.
    $log = tempnam(sys_get_temp_dir(), 'pitstop-vin-');
    $oldLog = ini_get('error_log');
    ini_set('error_log', $log);
    try {
        foreach ([new RuntimeException('timeout'), new RuntimeException('HTTP 429'), new JsonException('malformed JSON'), [], ['Results' => [['ErrorCode' => []]]]] as $result) {
            try { (new VinService(new StubHttpClient($result)))->decode('1HGCM82633A004352'); throw new RuntimeException('Upstream failure accepted.'); }
            catch (ExternalServiceException $e) { verify($e->getStatus() === 503 && str_contains($e->getMessage(), 'manually')); }
        }
        verify(substr_count(file_get_contents($log), 'VIN lookup failed:') === 5);
    } finally { ini_set('error_log', $oldLog); unlink($log); }
    echo "PASS: $checks VIN validation, response mapping and failure checks.\n";
} catch (Throwable $e) { fwrite(STDERR, 'FAIL: ' . $e->getMessage() . "\n"); exit(1); }
