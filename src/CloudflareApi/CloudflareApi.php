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
     */
    public static function getRecord(string $domain, array $record): ?array
    {
        $zone = static::getZone(domain:$domain)[0];

        $cfRecords = static::request(
            method:'get',
            path:"zones/{$zone['id']}/dns_records"
        );

        $matchingRecords = array_filter($cfRecords, function ($r) use ($domain, $record) {
            return
                $r['type'] === $record['type'] &&
                $r['name'] === $record['name'].'.'.$domain;
        });

        $matchingRecords = array_merge($matchingRecords);

        return $matchingRecords[0] ?? null;
    }

    /**
     * Gets the Cloudflare zone from the domain name.
     */
    public static function getZone(string $domain): array
    {
        return static::request(
            method:'get',
            path:'zones',
            data:[
                'name' => $domain,
            ]
        );
    }

    /**
     * Updates the Cloudflare record.
     */
    public static function updateRecord(array $cfRecord, array $record): array
    {
        return static::request(
            'put',
            "zones/{$cfRecord['zone_id']}/dns_records/{$cfRecord['id']}",
            [
                'type'    => $record['type'],
                'name'    => $cfRecord['name'],
                'content' => PublicIp::get(),
                'ttl'     => $record['ttl'],
                'proxied' => $record['proxied'],
            ]
        );
    }

    /**
     * Cloudflare API request base.
     */
    public static function request(
        string $method,
        string $path,
        ?array $data = []
    ): array {
        $response =
        Http::withToken(config('cloudflare_ddns.cloudflare_api_token'))
            ->$method(static::API_BASE.$path, $data);

        if ($response->failed()) {
            $errorMessage = $response->json()['errors'][0]['message'] ?? 'Cloudflare API request failed';

            throw
            CloudflareApiException::failedApiRequest(message:$errorMessage);
        }

        return $response->json()['result'];
    }
}
