# Cloudflare Dynamic DNS syncing in Laravel

[![Latest Version on Packagist](https://img.shields.io/packagist/v/calkeo/laravel_cloudflare_ddns.svg?style=flat-square)](https://packagist.org/packages/calkeo/laravel_cloudflare_ddns)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/calkeo/laravel_cloudflare_ddns/run-tests?label=tests)](https://github.com/calkeo/laravel_cloudflare_ddns/actions?query=workflow%3Arun-tests+branch%3Amaster)
[![GitHub Code Style Action Status](https://img.shields.io/github/workflow/status/calkeo/laravel_cloudflare_ddns/Check%20&%20fix%20styling?label=code%20style)](https://github.com/calkeo/laravel_cloudflare_ddns/actions?query=workflow%3A"Check+%26+fix+styling"+branch%3Amaster)
[![Total Downloads](https://img.shields.io/packagist/dt/calkeo/laravel_cloudflare_ddns.svg?style=flat-square)](https://packagist.org/packages/calkeo/laravel_cloudflare_ddns)


This package facilitates dynamic DNS (DDNS) for Cloudflare with no third-party integrations. The package interacts directly with the Cloudflare API to sync the current system's IP address with your Cloudflare DNS records.

## Use Cases

This package can be used for multiple purposes. For instance:

- Running a server on your home network that you want to be accessible to the public.
- Automatically updating your DNS records when you deploy or migrate a site to a new server.

## Installation

You can install the package via composer:

```bash
composer require calkeo/laravel_cloudflare_ddns
```

You can publish and run the migrations with:

```bash
php artisan vendor:publish --provider="Calkeo\Ddns\DdnsServiceProvider" --tag="laravel_cloudflare_ddns-migrations"
php artisan migrate
```

You can publish the config file with:
```bash
php artisan vendor:publish --provider="Calkeo\Ddns\DdnsServiceProvider" --tag="laravel_cloudflare_ddns-config"
```

This is the contents of the published config file:

```php
return [
];
```

## Usage

```php
$laravel_cloudflare_ddns = new Calkeo\Ddns();
echo $laravel_cloudflare_ddns->echoPhrase('Hello, Spatie!');
```

## Testing

```bash
composer test
```

## Roadmap
- ✅ Support A record dynamic DNS
- ⬜️ Add event broadcasting for DNS syncing


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
