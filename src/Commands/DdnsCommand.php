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
        $sync = new SyncJob();
        $this->comment("Synchronised DNS for {$sync->siteSyncCount} domains and {$sync->recordSyncCount} records");
    }
}
