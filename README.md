# Cloudflare Dynamic DNS with Laravel

[![Latest Stable Version](https://poser.pugx.org/calkeo/laravel-cloudflare-ddns/v)](//packagist.org/packages/calkeo/laravel-cloudflare-ddns) [![Total Downloads](https://poser.pugx.org/calkeo/laravel-cloudflare-ddns/downloads)](//packagist.org/packages/calkeo/laravel-cloudflare-ddns) [![License](https://poser.pugx.org/calkeo/laravel-cloudflare-ddns/license)](//packagist.org/packages/calkeo/laravel-cloudflare-ddns)
[![GitHub Code Style Action Status](https://github.styleci.io/repos/361263220/shield)](https://github.com/calkeo/laravel-cloudflare-ddns/actions?query=workflow%3A"Check+%26+fix+styling"+branch%3Amaster)


This package facilitates dynamic DNS (DDNS) for Cloudflare with no third-party integrations. The package interacts directly with the Cloudflare API to sync the current system's IP address with your Cloudflare DNS records.

## Use Cases

This package can be used for multiple purposes. For instance:

- Running a server on your home network that you want to be accessible to the public.
- Automatically updating your DNS records when you deploy or migrate a site to a new server.

## Prerequisites
- PHP >= 8.0
- Laravel >= 8

## Installation

Install the package via composer:

```bash
composer require calkeo/laravel-cloudflare-ddns
```

Publish the config file and migrations:

```bash
php artisan vendor:publish --provider="Calkeo\Ddns\DdnsServiceProvider" 
```

Run the migrations:

```bash
php artisan migrate
```

## Configuration

The configuration file can be found at `config/cloudflare_ddns`:

```php
return [

    /**
     * The Cloudflare API token for the account that has privileges to manage
     * the domains that you add to this file.
     */
    'cloudflare_api_token' => env('CLOUDFLARE_API_TOKEN'),

    /**
     * The amount of time in seconds that the public IP address is cached for
     * each time the ddns:sync command is executed.
     */
    'cache_duration' => env('DDNS_CACHE_DURATION', 60),

    /**
     * The domains to sync with this system's IP address.
     */
    'domains' => [
        [
            'domain' => '',
            // The domain that the DNS records will be synced with.
            'sync_interval' => 5,
            // The interval in minutes for which this domain's records will be updated
            'records' => [
                [
                    'name' => '', // DNS record name
                    'type' => '', // DNS record type
                    'ttl' => 1,
                    // Time to live for DNS record. Value of 1 is 'automatic'
                    'proxied' => true,
                    // Whether the DNS record is proxied through Cloudflare
                ],
            ],
        ],
    ],

];
```

### `cloudflare_api_token`

_(string)_

A Cloudflare API token that has sufficient privileges to access the DNS records for the domains listed in the config file. [How to generate a Cloudflare API token](https://developers.cloudflare.com/api/tokens/create).

---

### `cache_duration`

_(integer)_

The amount of time in seconds that the public IP address is cached for each time the `ddns:sync` command is executed.

The IP address is automatically flushed from the cache each time the `ddns:sync` command is executed. Because an external network request has to be made in order to retrieve the public IP address, increasing the cache duration will increase performance.

---

### `domains[]`

_(array)_

The domains to be synced. A domain is referred to as a _Zone_ within Cloudflare.

---

### `domains[][domain]`

_(string)_

The base domain name, also referred to as the _Zone_. This must be the base of the domain as any subdomains will be configured within the `records` array.

---

### `domains[][sync_interval]`

_(integer)_

How often (in minutes) the domain's DNS records will be synced with the server's public IP address.

---

### `domains[][records]`

_(array)_

The DNS records to be synced for the domain.

A record will be synced if there is already an existing DNS record in Cloudflare for this domain that matches the `type` and `name` specified for the record in the config file.

---

### `domains[][records][name]`

_(string)_

The DNS record name.

For instance:

- Record name `www` for `www.domain.com`
- Record name `mysubdomain` for `mysubdomain.domain.com` 

---

### `domains[][records][type]`

_(string)_

The DNS record type. Valid types are:

- `A`
- `AAAA`
- `CNAME`
- `HTTPS`
- `TXT`
- `SRV`
- `LOC`
- `MX`
- `NS`
- `SPF`
- `CERT`
- `DNSKEY`
- `DS`
- `NAPTR`
- `SMIMEA`
- `SSHFP`
- `SVCB`
- `TLSA`
- `URI`

---

### `domains[][records][ttl]`

_(integer)_

The DNS record TTL (time-to-live). Setting this value to `1` sets the DNS record TTL to 'automatic'.

---

### `domains[][records][proxied]`

_(boolean)_

Whether the DNS record should be proxied through Cloudflare's network.

A brief explanation of Cloudflare DNS proxying ([source](https://community.cloudflare.com/t/what-is-the-difference-between-proxied-and-dns-only/173310)):

> The DNS proxied means it will be shown a Cloudflare IP if you look it up. Thus all attacks at that domain will DDoS Cloudflare and not you host directly.
>
> Non proxied means all traffic goes directly to your own IP without Cloudflare being a safety net in front.
>
> The upside of proxied is that you will enjoy the Coudflare benefits but you can not make a direct connection to your IP, which means any custom ports wont work.
>
> Non proxied has the advantage of being able to use custom ports to connect as it will connect to your IP directly.

__If you are using this package to setup DDNS for your home network__, you should take note from the above, that if you proxy your IP address through Cloudflare then you cannot use custom ports. [Read about the ports available whilst proxying through Cloudflare](https://support.cloudflare.com/hc/en-us/articles/200169156-Identifying-network-ports-compatible-with-Cloudflare-s-proxy).

---
## Usage

To continually sync your configured domains, it is recommended to create a scheduled task that runs the `ddns:sync` command every minute.

```php
// App/Console/Kernel.php

protected function schedule(Schedule $schedule)
{
    $schedule->command('ddns:sync')->everyMinute();
}
```

The package will only sync with a domain in Cloudflare if the domain has not yet been synced, or if it is due to run based on the `sync_interval` value set for the domain in the config file.

An individual record will also not be updated if it is determined that there has been no change in IP address, and if the `ttl` and `proxied` values for the record in the config file are up-to-date in Cloudflare.

## Roadmap
| Feature | Status |
| ------- | ------ |
| Allow creation of new records | Upcoming |
| Support custom IP resolvers | Upcoming |
| Event Broadcasting | Upcoming |

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Callum Keogan](https://github.com/calkeo)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
