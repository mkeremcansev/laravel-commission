

## Installation![Laravel Commission](https://github.com/user-attachments/assets/d069ff03-adca-4893-b9ab-e4d8cd4324d8)


You can install the package via composer:

```bash
composer require mkeremcansev/laravel-commission
```

You can publish and run the migrations with:

```bash
php artisan vendor:publish --tag="laravel-commission-migrations"
php artisan migrate
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="laravel-commission-config"
```

This is the contents of the published config file:

```php
return [
];
```

Optionally, you can publish the views using

```bash
php artisan vendor:publish --tag="laravel-commission-views"
```

## Usage

```php
$laravelCommission = new Mkeremcansev\LaravelCommission();
echo $laravelCommission->echoPhrase('Hello, Mkeremcansev!');
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Mustafa Kerem CANSEV](https://github.com/mkeremcansev)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
