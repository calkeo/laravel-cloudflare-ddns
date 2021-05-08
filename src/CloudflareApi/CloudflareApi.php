<?php

namespace Calkeo\Ddns\CloudflareApi;

use Calkeo\Ddns\CloudflareApi\CloudflareRequest;
use Calkeo\Ddns\Exceptions\CloudflareApiException;
use Calkeo\Ddns\Tasks\PublicIp;
use Illuminate\Support\Facades\Http;

class CloudflareApi
{
    const API_BASE = "https://api.cloudflare.com/client/v4/";

    /**
     * Gets the current DNS record
     *
     * @param array $fields
     */
    public static function getRecord($fields)
    {
        $zone = static::getZone($fields['domain'])[0];

        $records = static::request('get', "zones/{$zone['id']}/dns_records");

        $matchingRecords = array_filter($records, function ($r) use ($fields) {
            return
                $r['type'] === $fields['records']['type'] &&
                $r['name'] === $fields['records']['name'] . '.' . $fields['domain'];
        });

        $matchingRecords = array_merge($matchingRecords);

        return $matchingRecords[0] ?? false;
    }

    /**
     * Gets the Cloudflare zone from the domain name
     *
     * @param  string  $domain
     * @return array
     */
    public static function getZone(string $domain): array
    {
        return static::request('get', 'zones', [
            'name' => $domain,
        ]);
    }

    /**
     * Updates the Cloudflare record
     *
     * @param  string  $record
     * @param  array   $fields
     * @return array
     */
    public static function updateRecord(array $record, array $fields): array
    {
        return static::request('put', "zones/{$record['zone_id']}/dns_records/{$record['id']}", [
            'type'    => $fields['records']['type'],
            'name'    => $record['name'],
            'content' => PublicIp::get(),
            'ttl'     => $fields['records']['ttl'],
            'proxied' => $fields['records']['proxied'],
        ]);
    }

    /**
     * Cloudflare API request base
     *
     * @param  string                            $method
     * @param  string                            $path
     * @param  string                            $data
     * @return Illuminate\Support\Facades\Http
     */
    public static function request(string $method, string $path, array $data = [])
    {
        $response = Http::withToken(config('cloudflare_ddns.cloudflare_api_token'))->$method(static::API_BASE . $path, $data);

        if ($response->failed()) {
            $errorMessage = $response->json()['errors'][0]['message'] ?? 'Cloudflare API request failed';

            throw CloudflareApiException::failedApiRequest($errorMessage);
        }

        return $response->json()['result'];
    }
}
