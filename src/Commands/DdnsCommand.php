<?php

namespace Calkeo\Ddns\Commands;

use Calkeo\Ddns\Tasks\Sync\SyncJob;
use Illuminate\Console\Command;

class DdnsCommand extends Command
{
    public $signature = 'ddns:sync';

    public $description = 'Synchronises DNS records with Cloudflare';

    public function handle()
    {
        $sync = new SyncJob;

        $recordString = $sync->recordSyncCount !== 1 ? 'records' : 'record';
        $domainString = $sync->domainSyncCount !== 1 ? 'domains' : 'domain';

        echo "Updated {$sync->recordSyncCount} DNS records for {$sync->domainSyncCount} {$domainString}" . PHP_EOL;
    }
}
