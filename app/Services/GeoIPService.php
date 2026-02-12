<?php

namespace App\Services;

class GeoIPService
{
    protected $reader = null;

    public function __construct()
    {
        $dbPath = storage_path('app/geoip/GeoLite2-City.mmdb');
        if (file_exists($dbPath) && class_exists(\GeoIp2\Database\Reader::class)) {
            $this->reader = new \GeoIp2\Database\Reader($dbPath);
        }
    }

    /**
     * البحث عن موقع IP
     * @return array{country: ?string, city: ?string, country_code: ?string}
     */
    public function lookup(string $ip): array
    {
        // 1. Check Private IP
        if ($this->isPrivateIP($ip)) {
            return ['country' => null, 'city' => null, 'country_code' => null];
        }

        // 2. Try MaxMind DB (Fastest)
        if ($this->reader) {
            try {
                $record = $this->reader->city($ip);
                return [
                    'country'      => $record->country->name ?? null,
                    'city'         => $record->city->name ?? null,
                    'country_code' => $record->country->isoCode ?? null,
                ];
            } catch(\Exception $e) {
                // Fallback to API if DB lookup fails
            }
        }

        // 3. Fallback to API (Cached)
        return \Illuminate\Support\Facades\Cache::remember("geoip_{$ip}", 86400 * 7, function () use ($ip) {
            try {
                $response = \Illuminate\Support\Facades\Http::timeout(2)
                    ->get("http://ip-api.com/json/{$ip}?fields=status,country,city,countryCode");
                
                if ($response->successful() && $response->json('status') === 'success') {
                    return [
                        'country'      => $response->json('country'),
                        'city'         => $response->json('city'),
                        'country_code' => $response->json('countryCode'),
                    ];
                }
            } catch (\Exception $e) {
                // Fail silently
            }

            return ['country' => null, 'city' => null, 'country_code' => null];
        });
    }

    protected function isPrivateIP(string $ip): bool
    {
        return !filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE);
    }
}
