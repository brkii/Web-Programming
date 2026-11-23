<?php
declare(strict_types=1);

class VinService
{
    public function __construct(private JsonHttpClient $client = new JsonHttpClient()) {}

    public function decode(mixed $vin, mixed $modelYear = null): array
    {
        $vin = Validator::vin($vin);
        $year = $modelYear === null ? null : Validator::integer($modelYear, 'model_year', 1980, 2155);
        $url = 'https://vpic.nhtsa.dot.gov/api/vehicles/DecodeVinValues/' . rawurlencode($vin) . '?format=json';
        if ($year !== null) { $url .= '&modelyear=' . $year; }
        try {
            $response = $this->client->get($url);
            $row = $response['Results'][0] ?? null;
            if (!is_array($row) || !array_key_exists('ErrorCode', $row)) { throw new RuntimeException('Unexpected NHTSA response structure.'); }
            $value = static function (string $key) use ($row): ?string {
                $text = $row[$key] ?? '';
                if (!is_string($text)) { throw new RuntimeException('Unexpected NHTSA field type.'); }
                return trim($text) === '' ? null : trim($text);
            };
            $codes = $value('ErrorCode');
            return [
                'vin' => $vin, 'make' => $value('Make'), 'model' => $value('Model'),
                'year' => ctype_digit($value('ModelYear') ?? '') ? (int) $row['ModelYear'] : null,
                'fuel_type' => $value('FuelTypePrimary'), 'body_class' => $value('BodyClass'),
                'source' => 'NHTSA vPIC',
                'complete' => $codes === '0' && $value('Make') !== null && $value('Model') !== null,
                'message' => $codes === '0' ? 'Review the decoded values before saving your vehicle.' : 'Some VIN details could not be decoded. Check the VIN and enter missing details manually.',
            ];
        } catch (Throwable $e) {
            error_log('VIN lookup failed: ' . $e->getMessage());
            throw new ExternalServiceException('VIN lookup is temporarily unavailable. You can enter vehicle details manually.', 0, $e);
        }
    }
}
