An opinionated Laravel 5 package to retrieve Google Analytics data.

## Install

This package can be installed through Composer.

``` bash
$ composer require spatie/laravel-analytics
```

You must install this service provider.

```php
    // config/app.php
    'provider' => [
        '...',
        'Spatie\LaravelAnalytics\LaravelAnalyticsServiceProvider',
    ];
```

This package also comes with a facade, which provides an easy way to call the the class.

```php
    // config/app.php
    'aliases' => [
        '...',
        'LaravelAnalytics' => 'Spatie\LaravelAnalytics\LaravelAnalyticsFacade',
    ];
```

You can publish the config file of this package using Artisan.

``` bash
    php vendor:publish --provider="spatie/laravelanalytics"
```

## Usage

Coming soon, this is a work in progress.

## Testing

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security

If you discover any security related issues, please email freek@spatie.be instead of using the issue tracker.

## Credits

- [Freek Van der Herten](https://github.com/freekmurze)
- [Matthias De Winter](https://github.com/MatthiasDeWinter)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.