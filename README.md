# Laravel Analytics

[![Latest Version](https://img.shields.io/github/release/freekmurze/laravel-analytics.svg?style=flat-square)](https://github.com/freekmurze/laravel-analytics/releases)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Quality Score](https://img.shields.io/scrutinizer/g/freekmurze/laravel-analytics.svg?style=flat-square)](https://scrutinizer-ci.com/g/freekmurze/laravel-analytics)
[![Total Downloads](https://img.shields.io/packagist/dt/spatie/laravel-analytics_name.svg?style=flat-square)](https://packagist.org/packages/spatie/laravel-analytics)

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

To obtain your Analytics Credentials start by going to the [Google Developers Console](https://console.developers.google.com) and choosing the correct 'project'.

Next, click APIs under APIs & auth (_left side-menu_) and enable the Analytics API.
Now, under the same submenu click Credentials.
In here create a new oAuth 2.0 Client Id with the service account setting.

This will automatically create the needed keys and download the .p12 certificate.

Now the service_email and client_id will be listed on right side as CLIENT ID and EMAIL ADDRESS.

To find your siteId log in to [Google Analytics](http://www.google.be/intl/en/analytics/) and go the the _Admin_ section.
In the property-column select the right website name, then click View Settings in the View-column.

The View Id is what we call siteId. To use this in our configuration prepend it with 'ga:'.
So a View Id of 12345678 would become ga:12345678.

## Usage

When the installation is done you can easily retrieve Analytics data. Mostly all methods will return an `Illuminate\Support\Collection`-instance.


Here is an example to retrieve visitors and pageview data for the last seven days.
```php
/*
* $analyticsData now contains a Collection with 3 columns: "date", "visitors" and "pageViews"
*/
$analyticsData = LaravelAnalytics::getVisitorsAndPageViews(7)
```

Here's another example to get the 20 most visited pages of the last 365 days
```php
/*
* $analyticsData now contains a Collection with 2 columns: "url" and "pageViews"
*/
$analyticsData = LaravelAnalytics::getMostVisitedPages(365, 20)
```
## Provided methods

###Visitors and Pageviews
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

###Keywords
These methods return a Collection with columns "keyword" and "sessions".
```php
   /**
     * Get the top keywords
     *
     * @param int $numberOfDays
     * @param int $maxResults
     * @return Collection
     */
    public function getTopKeywords($numberOfDays = 365, $maxResults = 30)

    /**
     * Get the top keywords for the given period
     *
     * @param \DateTime $startDate
     * @param \DateTime $endDate
     * @param int $maxResults
     * @return Collection
     */
    public function getTopKeyWordsForPeriod($startDate, $endDate, $maxResults = 30)
```

###Referrers
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

###Browsers
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

###Most visited pages
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

###All other Google Analytics Queries
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
