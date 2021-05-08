<?php

return [

    /**
     * The Cloudflare API token for the account that has privileges to manage
     * the domains that you add to this file.
     */
    'cloudflare_api_token' => env('CLOUDFLARE_API_TOKEN'),

    /**
     * The amount of time in seconds that the public IP address is cached for each time
     * the ddns:sync command is executed.
     *
     * The IP address is automatically flushed from the cache each time the ddns:sync command
     * is executed. Increasing the cache duration can increase performance when syncing a
     * large number of domains.
     */
    'cache_duration' => env('DDNS_CACHE_DURATION', 60),

    /**
     * The domains to sync with this system's IP address
     */
    'domains' => [
        [
            'domain'        => '', // The domain that the DNS records will be synced with.
            'records' => [
                [
                    'name' => '', // DNS record name
                    'type' => '', // DNS record type
                    'ttl' => 1, // Time to live for DNS record. Value of 1 is 'automatic'
                    'proxied' => true, // Whether the DNS record is proxied through Cloudflare
                    'create_if_missing' => false, // Whether the record will be created automatically if it doesn't already exist
                    'exception_if_missing' => true, // Whether missing records will throw an exception if they are missing //TODO: Document that this is overwritten  by the above setting
                ],
            ],
            'sync_interval' => 5, // The interval in minutes for which this domain's records will be updated
        ],
    ],

];
