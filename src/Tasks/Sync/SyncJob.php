<?php

namespace Calkeo\Ddns\Tasks\Sync;

use Calkeo\Ddns\Exceptions\InvalidConfigurationException;
use Calkeo\Ddns\Models\CloudflareRecord;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;

class SyncJob
{
    protected $cloudFlareApiToken;

    protected $domains;

    protected $siteSyncCount;

    protected $recordSyncCount;

    protected $missingRecordCount;

    public function __construct()
    {
        $this->setOptions();
        $this->run();
    }

    /**
     * Retrieves the config file, validates it and assigns values to class attributes
     *
     * @return void
     */
    private function setOptions(): void
    {
        $options = config('cloudflare_ddns');

        $validator = Validator::make($options, [
            'cloudflare_api_token'                  => [
                'required',
                'string',
            ],
            'cache_duration'                        => [
                'required',
                'integer',
                'min:0',
            ],
            'domains'                               => [
                'required',
                'array',
                'min:1',
            ],
            'domains.*.domain'                      => [
                'required',
                'string',
            ],
            'domains.*.records'                     => [
                'required',
                'array',
                'min:1',
            ],
            'domains.*.records.*.name'              => [
                'required',
                'string',
            ],
            'domains.*.records.*.type'              => [
                'required',
                'string',
            ],
            'domains.*.records.*.ttl'               => [
                'required',
                'integer',
            ],
            'domains.*.records.*.proxied'           => [
                'required',
                'boolean',
            ],
            'domains.*.records.*.create_if_missing' => [
                'required',
                'boolean',
            ],
        ], [
            'required' => ':attribute is required',
            'string'   => ':attribute must be a string',
            'array'    => ':attribute must be an array',
            'min'      => ':attribute must have at least :min element',
            'integer'  => ':attribute must be an integer',
        ]);

        if ($validator->fails()) {
            throw InvalidConfigurationException::invalidData($validator->errors()->toArray());
        }

        $this->domains = $options['domains'];
    }

    /**
     * Starts the DDNS sync
     *
     * @return void
     */
    private function run(): void
    {
        Cache::forget('laravelCloudflareDdnsPublicIp');

        foreach ($this->domains as $domain) {
            $this->syncDomain($domain);
        }
    }

    /**
     * Syncs the domain's records
     *
     * @param  array  $domain
     * @return void
     */
    private function syncDomain(array $domain): void
    {
        foreach ($domain['records'] as $record) {
            $cfRecord = CloudflareRecord::get($domain['domain'], $record);

            if ($cfRecord) {
                $cfRecord->update($record);
            }
        }
    }
}