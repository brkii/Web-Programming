<?php
declare(strict_types=1);

class JsonHttpClient
{
    public function get(string $url): array
    {
        $curl = curl_init($url);
        $body = '';
        curl_setopt_array($curl, [
            CURLOPT_CONNECTTIMEOUT => 3,
            CURLOPT_TIMEOUT => 10,
            CURLOPT_PROTOCOLS => CURLPROTO_HTTPS,
            CURLOPT_FOLLOWLOCATION => false,
            CURLOPT_HTTPHEADER => ['Accept: application/json'],
            CURLOPT_USERAGENT => 'PitStopOS/1.0 (university project)',
            CURLOPT_WRITEFUNCTION => static function ($handle, string $chunk) use (&$body): int {
                if (strlen($body) + strlen($chunk) > 1048576) { return 0; }
                $body .= $chunk;
                return strlen($chunk);
            },
        ]);
        try {
            if (curl_exec($curl) === false) { throw new RuntimeException('External HTTP transport failed, curl code ' . curl_errno($curl)); }
            $status = curl_getinfo($curl, CURLINFO_RESPONSE_CODE);
            if ($status !== 200) { throw new RuntimeException('External HTTP status ' . $status); }
            $data = json_decode($body, true, 64, JSON_THROW_ON_ERROR);
            if (!is_array($data)) { throw new RuntimeException('External response is not an object.'); }
            return $data;
        } finally { curl_close($curl); }
    }
}
