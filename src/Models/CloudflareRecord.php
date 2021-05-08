<?php

namespace Calkeo\Ddns\Models;

use Calkeo\Ddns\CloudflareApi\CloudflareApi;
use Calkeo\Ddns\Tasks\PublicIp;

class CloudflareRecord
{
    protected $cfRecord;

    private function __construct(array $cfRecord)
    {
        $this->cfRecord = $cfRecord;
    }

    /**
     * Retrieves the record from Cloudflare
     *
     * @param  string  $domain
     * @param  array   $record
     * @return mixed
     */
    public static function get(string $domain, array $record)
    {
        $cfRecord = CloudflareApi::getRecord($domain, $record);

        return $cfRecord ? new static($cfRecord) : false;
    }

    /**
     * Updates the record in Cloudflare
     *
     * @param  array  $fields
     * @return void
     */
    public function update(array $fields): void
    {
        $response = CloudflareApi::updateRecord($this->cfRecord, $fields);
    }

    /**
     * Determines whether the Cloudflare record contains the same information as the given record array
     *
     * @param  array  $record
     * @return bool
     */
    public function isDifferentTo(array $record): bool
    {
        return (
            $this->cfRecord['content'] !== PublicIp::get()
            ||
            $this->cfRecord['ttl'] !== $record['ttl']
            ||
            $this->cfRecord['proxied'] !== $record['proxied']
        );
    }
}