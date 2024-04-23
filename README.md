# Filament plugin for language/translations management

[![Latest Version on Packagist](https://img.shields.io/packagist/v/cube-agency/filament-translations.svg?style=flat-square)](https://packagist.org/packages/cube-agency/filament-translations)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/cube-agency/filament-translations/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/cube-agency/filament-translations/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/cube-agency/filament-translations/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/cube-agency/filament-translations/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/cube-agency/filament-translations.svg?style=flat-square)](https://packagist.org/packages/cube-agency/filament-translations)

Adds Language/Translations resources with import/export functionality.

## Installation

You can install the package via composer:

```bash
composer require cube-agency/filament-translations
```

Run migrations:

```bash
php artisan migrate
```

Add the plugin to your panel provider:
```bash
use CubeAgency\FilamentTranslations\FilamentTranslationsPlugin;

public function panel(Panel $panel): Panel
{
    return $panel
        ...
        ->plugins([
            ...
            FilamentTranslationsPlugin::make()
        ]);
}
```

Optionally, you can publish the views using

```bash
php artisan vendor:publish --tag="filament-translations-views"
```

## Usage
- Add language(s) in Language resource
- Run import command to load translations from language (/lang) files
```bash
php artisan translator:load
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

- [Dmitrijs Mihailovs](https://github.com/cube-agency)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
