<?php

namespace Calkeo\Ddns\CloudflareApi;

use Calkeo\Ddns\Exceptions\CloudflareApiException;
use Calkeo\Ddns\Tasks\PublicIp;
use Illuminate\Support\Facades\Http;

class CloudflareApi
{
    const API_BASE = 'https://api.cloudflare.com/client/v4/';

    /**
     * Gets the current DNS record.
     *
     * @param string $domain
     * @param array  $record
     */
    public static function getRecord(string $domain, array $record)
    {
        $zone = static::getZone($domain)[0];

        $cfRecords = static::request('get', "zones/{$zone['id']}/dns_records");

        $matchingRecords = array_filter($cfRecords, function ($r) use ($domain, $record) {
            return
                $r['type'] === $record['type'] &&
                $r['name'] === $record['name'].'.'.$domain;
        });

        $matchingRecords = array_merge($matchingRecords);

        return $matchingRecords[0] ?? false;
    }

    /**
     * Gets the Cloudflare zone from the domain name.
     *
     * @param string $domain
     *
     * @return array
     */
    public static function getZone(string $domain): array
    {
        return static::request('get', 'zones', [
            'name' => $domain,
        ]);
    }

    /**
     * Updates the Cloudflare record.
     *
     * @param string $cfRecord
     * @param array  $record
     *
     * @return array
     */
    public static function updateRecord(array $cfRecord, array $record): array
    {
        return static::request('put', "zones/{$cfRecord['zone_id']}/dns_records/{$cfRecord['id']}", [
            'type'    => $record['type'],
            'name'    => $cfRecord['name'],
            'content' => PublicIp::get(),
            'ttl'     => $record['ttl'],
            'proxied' => $record['proxied'],
        ]);
    }

    /**
     * Cloudflare API request base.
     *
     * @param string $method
     * @param string $path
     * @param string $data
     *
     * @return Illuminate\Support\Facades\Http
     */
    public static function request(string $method, string $path, array $data = [])
    {
        $response = Http::withToken(config('cloudflare_ddns.cloudflare_api_token'))->$method(static::API_BASE.$path, $data);

        if ($response->failed()) {
            $errorMessage = $response->json()['errors'][0]['message'] ?? 'Cloudflare API request failed';

            throw CloudflareApiException::failedApiRequest($errorMessage);
        }

        return $response->json()['result'];
    }
}
