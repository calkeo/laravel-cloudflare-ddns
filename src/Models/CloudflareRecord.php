<?php

namespace Calkeo\Ddns\Models;

use Calkeo\Ddns\CloudflareApi\CloudflareApi;
use Calkeo\Ddns\Tasks\PublicIp;

class CloudflareRecord
{

    private function __construct(
        public array $cfRecord
    ) {}

    /**
     * Retrieves the record from Cloudflare.
     */
    public static function get(string $domain, array $record): ?static
    {
        $cfRecord = CloudflareApi::getRecord(
            domain:$domain,
            record:$record
        );

        return $cfRecord ? new static($cfRecord) : null;
    }

    /**
     * Updates the record in Cloudflare.
     */
    public function update(array $fields): void
    {
        $response = CloudflareApi::updateRecord(
            cfRecord:$this->cfRecord,
            record:$fields,
        );
    }

    /**
     * Determines whether the Cloudflare record contains the same information as the given record array.
     */
    public function isDifferentTo(array $record): bool
    {
        return
        $this->cfRecord['content'] !== PublicIp::get()
        ||
        $this->cfRecord['ttl'] !== $record['ttl']
        ||
        $this->cfRecord['proxied'] !== $record['proxied'];
    }
}
