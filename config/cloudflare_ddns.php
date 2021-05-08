<?php

return [

    /**
     * The Cloudflare API token for the account that has privileges to manage
     * the domains that you add to this file.
     */
    'cloudflare_api_token' => env('CLOUDFLARE_API_TOKEN'),

    /**
     * The amount of time in seconds that the public IP address is cached for
     * each time the ddns:sync command is executed.
     */
    'cache_duration' => env('DDNS_CACHE_DURATION', 60),

    /**
     * The domains to sync with this system's IP address.
     */
    'domains' => [
        [
            'domain' => '',
            // The domain that the DNS records will be synced with.
            'sync_interval' => 5,

            // The interval in minutes for which this domain's records will be updated
            'records' => [
                [
                    'name' => '', // DNS record name
                    'type' => '', // DNS record type
                    'ttl' => 1,
                    // Time to live for DNS record. Value of 1 is 'automatic'
                    'proxied' => true,
                    // Whether the DNS record is proxied through Cloudflare
                ],
            ],
        ],
    ],

];
