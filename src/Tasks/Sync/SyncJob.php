<?php

namespace Calkeo\Ddns\Tasks\Sync;

use Calkeo\Ddns\Exceptions\InvalidConfigurationException;
use Calkeo\Ddns\Models\CloudflareRecord;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class SyncJob
{
    protected $domains;

    public $domainSyncCount = 0;

    public $recordSyncCount = 0;

    public function __construct()
    {
        $this->setOptions();
        $this->run();
    }

    /**
     * Retrieves the config file, validates it and assigns values to class attributes.
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
     * Starts the DDNS sync.
     *
     * @return void
     */
    private function run(): void
    {
        Cache::forget('laravelCloudflareDdnsPublicIp');

        foreach ($this->domains as $domain) {
            $dbRecord = DB::table('cloudflare_ddns')->where('domain', $domain['domain'])->first();

            if (!$dbRecord || static::dueSync($domain, $dbRecord)) {
                $this->syncDomain($domain);
                $this->domainSyncCount++;

                DB::table('cloudflare_ddns')->updateOrInsert(
                    ['domain' => $domain['domain']],
                    ['last_sync' => now()]
                );
            }
        }
    }

    /**
     * Syncs the domain's records.
     *
     * @param array $domain
     *
     * @return void
     */
    private function syncDomain(array $domain): void
    {
        foreach ($domain['records'] as $record) {
            $cfRecord = CloudflareRecord::get($domain['domain'], $record);

            if ($cfRecord && $cfRecord->isDifferentTo($record)) {
                $cfRecord->update($record);
                $this->recordSyncCount++;
            }
        }
    }

    /**
     * Determines whether the domain is due for syncing.
     *
     * @param array      $domain
     * @param Collection $dbRecord
     *
     * @return bool
     */
    private static function dueSync(array $domain, $dbRecord)
    {
        return now()->diffInMinutes($dbRecord->last_sync) > $domain['sync_interval'];
    }
}
