<?php

namespace Calkeo\Ddns\Tasks;

use Calkeo\Ddns\Exceptions\PublicIpException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class PublicIp
{

    /**
     * Gets the public IP address
     *
     * @return string
     */
    public static function get()
    {
        return Cache::remember('laravelCloudflareDdnsPublicIp', config('cloudflare_ddns.cache_duration'), function () {
            $ipifyResponse = Http::get("https://api.ipify.org?format=json");

            if ($ipifyResponse->successful()) {
                return $ipifyResponse->json()['ip'];
            }

            throw PublicIpException::failedApiRequest("Failed to get server IP address");
        });
    }
}