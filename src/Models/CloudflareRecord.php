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
     * @param  array   $fields
     * @return mixed
     */
    public static function get(array $fields)
    {
        $cfRecord = CloudflareApi::getRecord($fields);

        return $cfRecord ? new static($cfRecord) : $this->handleMissingRecord($fields);
    }

    /**
     * Handle a missing Cloudflare record
     *
     * @param  array   $fields
     * @return mixed
     */
    private function handleMissingRecord(array $fields)
    {
        if ($fields['create_if_missing']) {
            return $this->createRecord();
        } elseif ($fields['error_on_missing']) {
            throw Exception();
        }

        return false;
    }
}
