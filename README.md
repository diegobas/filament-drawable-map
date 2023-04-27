# FilamentPHP field to draw polygon on a Google Map

[![Latest Version on Packagist](https://img.shields.io/packagist/v/diegobas/filament-drawable-map.svg?style=flat-square)](https://packagist.org/packages/diegobas/filament-drawable-map)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/diegobas/filament-drawable-map/run-tests?label=tests)](https://github.com/diegobas/filament-drawable-map/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/workflow/status/diegobas/filament-drawable-map/Check%20&%20fix%20styling?label=code%20style)](https://github.com/diegobas/filament-drawable-map/actions?query=workflow%3A"Check+%26+fix+styling"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/diegobas/filament-drawable-map.svg?style=flat-square)](https://packagist.org/packages/diegobas/filament-drawable-map)



This is where your description should go. Limit it to a paragraph or two. Consider adding a small example.

## Installation

You can install the package via composer:

```bash
composer require diegobas/filament-drawable-map
```

You can publish and run the migrations with:

```bash
php artisan vendor:publish --tag="filament-drawable-map-migrations"
php artisan migrate
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="filament-drawable-map-config"
```

Optionally, you can publish the views using

```bash
php artisan vendor:publish --tag="filament-drawable-map-views"
```

This is the contents of the published config file:

```php
return [
];
```

## Usage

```php
$filament-drawable-map = new DiegoBas\FilamentDrawableMap();
echo $filament-drawable-map->echoPhrase('Hello, DiegoBas!');
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Diego Bas Rius](https://github.com/diegobas)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
