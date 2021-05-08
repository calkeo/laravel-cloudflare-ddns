<?php

namespace Calkeo\Ddns;

use Calkeo\Ddns\Commands\DdnsCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class DdnsServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-cloudflare-ddns')
            ->hasConfigFile('cloudflare_ddns')
            ->hasMigration('create_cloudflare_ddns_table')
            ->hasCommand(DdnsCommand::class);
    }
}