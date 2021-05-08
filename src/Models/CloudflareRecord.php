<?php

namespace Calkeo\Ddns\Models;

use Calkeo\Ddns\CloudflareApi\CloudflareApi;

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
}