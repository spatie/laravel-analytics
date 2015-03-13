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
    php artisan vendor:publish --provider="Spatie\LaravelAnalytics\LaravelAnalyticsServiceProvider"
```

And now fill in your _Analytics credentials_ in config/laravel-analytics.php.

## Analytics Credentials ##

To obtain your Analytics Credentials start by going to the [Google Developers Console](https://console.developers.google.com).

Next, click __APIs__ under __APIs & auth__ (_left side-menu_) and enable the __Analytics API__.
Now, under the same submenu click __Credentials__.
In here create a new __oAuth 2.0 Client Id__ with the  __service account__ setting.

This will automatically create the needed keys and download the __.p12 certificate__.

Now the **service_email* and *client_id** will be listed on right side as _CLIENT ID_ and _EMAIL ADDRESS_.

To find your __siteId__ log in to [Google Analytics](http://www.google.be/intl/en/analytics/) and go the the _Admin_ section.
In the property-column select the right website name, then click _View Settings_ in the _View-column_.

The **View Id** is what we call siteId. To use this in our configuration prepend it with 'ga:'.
So a View Id of 12345678 would become ga:12345678.

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
