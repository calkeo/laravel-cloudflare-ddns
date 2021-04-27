<?php

namespace Calkeo\Ddns;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Calkeo\Ddns\Ddns
 */
class DdnsFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'laravel_cloudflare_ddns';
    }
}
