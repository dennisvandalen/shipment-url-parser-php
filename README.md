# This is my package shipment-url-parser-php

[![Latest Version on Packagist](https://img.shields.io/packagist/v/dennisvandalen/shipment-url-parser-php.svg?style=flat-square)](https://packagist.org/packages/dennisvandalen/shipment-url-parser-php)
[![Tests](https://github.com/dennisvandalen/shipment-url-parser-php/actions/workflows/run-tests.yml/badge.svg?branch=main)](https://github.com/dennisvandalen/shipment-url-parser-php/actions/workflows/run-tests.yml)
[![Total Downloads](https://img.shields.io/packagist/dt/dennisvandalen/shipment-url-parser-php.svg?style=flat-square)](https://packagist.org/packages/dennisvandalen/shipment-url-parser-php)

This is where your description should go. Try and limit it to a paragraph or two. Consider adding a small example.

## Installation

You can install the package via composer:

```bash
composer require dennisvandalen/shipment-url-parser-php
```

## Usage

```php
$shipment = (new ShipmentUrlParser())
    ->parse('https://dhlparcel.nl/en/private/receiving/follow-your-shipment?tt=TRACKING_CODE&pc=ZIPCODE');

//DennisVanDalen\ShipmentUrlParser\DTO\Shipment {
//  +url: "https://dhlparcel.nl/en/private/receiving/follow-your-shipment?tt=TRACKING_CODE&pc=ZIPCODE"
//  +trackingCode: "TRACKING_CODE"
//  +carrier: "DHL"
//  +carrierName: "DHL"
//}
```

## Todo

- [ ] Handle unknown urls

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

- [Dennis van Dalen](https://github.com/dennisvandalen)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
