#  Retrieve data from Google Analytics

[![Latest Version](https://img.shields.io/github/release/spatie/laravel-analytics.svg?style=flat-square)](https://github.com/spatie/laravel-analytics/releases)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Build Status](https://img.shields.io/travis/spatie/laravel-analytics/master.svg?style=flat-square)](https://travis-ci.org/spatie/laravel-analytics)
[![Quality Score](https://img.shields.io/scrutinizer/g/spatie/laravel-analytics.svg?style=flat-square)](https://scrutinizer-ci.com/g/spatie/laravel-analytics)
[![Total Downloads](https://img.shields.io/packagist/dt/spatie/laravel-analytics.svg?style=flat-square)](https://packagist.org/packages/spatie/laravel-analytics)

Using this package you can easily retrieve data from Google Analytics.

Here are a few examples of the provided methods:

```php
Analytics::getMostVisistedPages();
Analytics::getVisitorsAndPageViews();
```

Most methods will return an `\Illuminate\Support\Collection` object containing the results.

Spatie is a webdesign agency in Antwerp, Belgium. You'll find an overview of all our open source projects [on our website](https://spatie.be/opensource).

## Install

This package can be installed through Composer.

``` bash
composer require spatie/laravel-analytics
```

You must install this service provider.

```php
// config/app.php
'providers' => [
    ...
    Spatie\Analytics\AnalyticsServiceProvider::class,
    ...
];
```

This package also comes with a facade, which provides an easy way to call the the class.

```php
// config/app.php
'aliases' => [
    ...
    'Analytics' => Spatie\Analytics\AnalyticsFacade::class,
    ...
];
```

You can publish the config file of this package with this command:

``` bash
php artisan vendor:publish --provider="Spatie\Analytics\AnalyticsServiceProvider"
```

The following config file will be published in `config/laravel-analytics.php`
```php

return [
    
    /*
     * The view id of which you want to display data.
     */
    'view_id' => env('ANALYTICS_VIEW_ID'),

    /*
     * Path to the client secret json file. Take a look at the README of this package
     * to learn how to get this file.
     */
    'service_account_credentials_json' => storage_path('app/laravel-google-analytics/service-account-credentials.json'),

    /*
     * The amount of minutes the Google API responses will be cached.
     * If you set this to zero, the responses won't be cached at all.
     */
    'cache_lifetime_in_minutes' => 60 * 24,
];
```

### How to obtain the credentials to communicate with Google Analytics

Coming soon.

## Usage

When the installation is done you can easily retrieve Analytics data. Mostly all methods will return an `Illuminate\Support\Collection`-instance.


Here is an example to retrieve visitors and pageview data for the last seven days.
```php
/*
* $analyticsData now contains a Collection with 3 columns: "date", "visitors" and "pageViews"
*/
$analyticsData = Analytics::getVisitorsAndPageViews(7);
```

Here's another example to get the 20 most visited pages of the last 365 days
```php
/*
* $analyticsData now contains a Collection with 2 columns: "url" and "pageViews"
*/
$analyticsData = Analytics::getMostVisitedPages(365, 20);
```
## Provided methods

### Visitors and pageviews

These methods return a Collection with columns "date", "vistors" and "pageViews". When grouping by yearMonth, the first column will be called "yearMonth".
```php
    /**
     * Get the amount of visitors and pageviews
     *
     * @param int $numberOfDays
     * @param string $groupBy Possible values: date, yearMonth
     * @return Collection
     */
    public function getVisitorsAndPageViews($numberOfDays = 365, $groupBy = 'date')

    /**
     * Get the amount of visitors and pageviews for the given period
     *
     * @param \DateTime $startDate
     * @param \DateTime $endDate
     * @param string $groupBy Possible values: date, yearMonth
     * @return Collection
     */
    public function getVisitorsAndPageViewsForPeriod($startDate, $endDate, $groupBy = 'date')
```    


### Referrers

These methods return a Collection with columns "url" and "pageViews".
```php
    /**
     * Get the top referrers
     *
     * @param int $numberOfDays
     * @param int $maxResults
     * @return Collection
     */
    public function getTopReferrers($numberOfDays = 365, $maxResults = 20)

    /**
     * Get the top referrers for the given period
     *
     * @param \DateTime $startDate
     * @param \DateTime $endDate
     * @param $maxResults
     * @return Collection
     */
    public function getTopReferrersForPeriod($startDate, $endDate, $maxResults)
``` 

### Browsers

These methods return a Collection with columns "browser" and "sessions".

If there are  more used browsers than the number specified in maxResults, then a new resultrow with browser-name "other" will be appended with a sum of all the remaining browsers.
```php
    /**
     * Get the top browsers
     *
     * @param int $numberOfDays
     * @param int $maxResults
     * @return Collection
     */
    public function getTopBrowsers($numberOfDays = 365, $maxResults = 6)
    
    /**
     * Get the top browsers for the given period
     *
     * @param \DateTime $startDate
     * @param \DateTime $endDate
     * @param $maxResults
     * @return Collection
     */
    public function getTopBrowsersForPeriod($startDate, $endDate, $maxResults) 
```     

### Most visited pages

These methods return a Collection with columns "url" and "pageViews".
```php
    /**
     * Get the most visited pages
     *
     * @param int $numberOfDays
     * @param int $maxResults
     * @return Collection
     */
    public function getMostVisitedPages($numberOfDays = 365, $maxResults = 20)
    
    /**
     * Get the most visited pages for the given period
     *
     * @param \DateTime $startDate
     * @param \DateTime $endDate
     * @param int $maxResults
     * @return Collection
     */
    public function getMostVisitedPagesForPeriod($startDate, $endDate, $maxResults = 20)
```

   
### All other Google Analytics queries

To perform all other GA queries use  ```performQuery```.  [Google's Core Reporting API](https://developers.google.com/analytics/devguides/reporting/core/v3/common-queries) provides more information on on which metrics and dimensions might be used. 
```php
    /**
     * Call the query method on the autenthicated client
     *
     * @param \DateTime $startDate
     * @param \DateTime $endDate
     * @param $metrics
     * @param array $others
     * @return mixed
     */
    public function performQuery($startDate, $endDate, $metrics, $others = array())
```    

### Manually setting the siteId at runtime.

You can also set the `viewId` at runtime. This can be useful if you need to get statistics from multiple sites in one project.
```php
 Analytics::setViewId('1234567')->getVisitorsAndPageViews(); // will use the given siteId
 LaravelAnalytics->getVisitorsAndPageViews();` // will use the siteId specified in the config file.
 ```

## Testing

Run the tests with:

``` bash
vendor/bin/phpunit
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security

If you discover any security related issues, please email freek@spatie.be instead of using the issue tracker.

## Credits

- [Freek Van der Herten](https://github.com/freekmurze)
- [Matthias De Winter](https://github.com/MatthiasDeWinter)
- [All Contributors](../../contributors)

## About Spatie
Spatie is a webdesign agency in Antwerp, Belgium. You'll find an overview of all our open source projects [on our website](https://spatie.be/opensource).

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
