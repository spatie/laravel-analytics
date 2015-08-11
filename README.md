#  Retrieve data from Google Analytics

[![Latest Version](https://img.shields.io/github/release/spatie/laravel-analytics.svg?style=flat-square)](https://github.com/spatie/laravel-analytics/releases)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Build Status](https://img.shields.io/travis/spatie/laravel-analytics/master.svg?style=flat-square)](https://travis-ci.org/spatie/laravel-analytics)
[![Quality Score](https://img.shields.io/scrutinizer/g/spatie/laravel-analytics.svg?style=flat-square)](https://scrutinizer-ci.com/g/spatie/laravel-analytics)
[![Total Downloads](https://img.shields.io/packagist/dt/spatie/laravel-analytics.svg?style=flat-square)](https://packagist.org/packages/spatie/laravel-analytics)

This is an opinionated Laravel 5 package to retrieve Google Analytics data.

Spatie is a webdesign agency in Antwerp, Belgium. You'll find an overview of all our open source projects [on our website](https://spatie.be/opensource).

## Install

This package can be installed through Composer.

``` bash
composer require spatie/laravel-analytics
```

You must install this service provider.

```php
// config/app.php
'provider' => [
    ...
    'Spatie\LaravelAnalytics\LaravelAnalyticsServiceProvider',
    ...
];
```

This package also comes with a facade, which provides an easy way to call the the class.

```php
// config/app.php
'aliases' => [
    ...
    'LaravelAnalytics' => 'Spatie\LaravelAnalytics\LaravelAnalyticsFacade',
    ...
];
```

You can publish the config file of this package with this command:

``` bash
php artisan vendor:publish --provider="Spatie\LaravelAnalytics\LaravelAnalyticsServiceProvider"
```

The following config file will be published in `config/laravel-analytics.php`
```php
return

    [
        /*
         * The siteId is used to retrieve and display Google Analytics statistics
         * in the admin-section.
         *
         * Should look like: ga:xxxxxxxx.
         */
        'siteId' => env('ANALYTICS_SITE_ID'),

        /*
         * Set the client id
         *
         * Should look like:
         * xxxxxxxxxxxx-xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx.apps.googleusercontent.com
         */
        'clientId' => env('ANALYTICS_CLIENT_ID'),

        /*
         * Set the service account name
         *
         * Should look like:
         * xxxxxxxxxxxx-xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx@developer.gserviceaccount.com
         */
        'serviceEmail' => env('ANALYTICS_SERVICE_EMAIL'),

        /*
         * You need to download a p12-certifciate from the Google API console
         * Be sure to store this file in a secure location.
         */
        'certificatePath' => storage_path('laravel-analytics/xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx-privatekey.p12'),

        /*
         * The amount of minutes the Google API responses will be cached.
         * If you set this to zero, the responses won't be cached at all.
         */
        'cacheLifetime' => 60 * 24 * 2,
        
        /*
         * The amount of seconds the Google API responses will be cached for
         * queries that use the real time query method. If you set this to zero,
         * the responses of real time queries won't be cached at all.
         */
        'realTimeCacheLifetimeInSeconds' => 5,
    ];
```

### How to obtain the credentials to communicate with Google Analytics

If you haven't already done so, [set up a Google Analtyics property](https://support.google.com/analytics/answer/1042508) and [install the tracking code on your site](https://support.google.com/analytics/answer/1008080?hl=en#GA).

This package needs valid configuration values for `siteId`, `clientId` and `serviceEmail`. Additionally a `p12-file`is required.
 
To obtain these credentials start by going to the [Google Developers Console](https://console.developers.google.com).

If you don't have a project present in the console yet, create one.
If you click on the project name, you'll see a menu item `APIs` under `APIs & auth` on the left hand side. Click it to go the the Enabled API's screen. On that screen you should enable the Analytics API.
Now, again under the `APIs & Auth`-menu click `Credentials`.
On this screen you should press `Create new Client ID`. In the creation screen make sure you select application type `Service Account` and key type `P12-key`.

This wil generate a new public/private key pair and the .p12-file will get downloaded to your machine. Store this file in the location specified in the configfile of this package.

In the properties of the newly created Service Account you'll find the values for the `serviceEmail` and `clientId` listed as `CLIENT ID` and `EMAIL ADDRESS`.

To find the right value for `siteId` log in to [Google Analytics](http://www.google.be/intl/en/analytics/) go the the Admin section.
In the property-column select the website name of which you want to retrieve data, then click `View Settings` in the `View`-column.
The value presented as `View Id` prepended with 'ga:' can be used as `siteId`.

Make sure you've added the `ANALYTICS_SERVICE_EMAIL` to the Google Analytics Account otherwise you will get a `403: User does not have any Google Analytics Account` error. [You can read Google's instructions here](http://support.google.com/analytics/bin/answer.py?hl=en&answer=1009702).

If you want to use the realtime methods you should [request access](https://docs.google.com/forms/d/1qfRFysCikpgCMGqgF3yXdUyQW4xAlLyjKuOoOEFN2Uw/viewform) to the beta version of [Google's Real Time Reporting API](https://developers.google.com/analytics/devguides/reporting/realtime/v3/).

## Usage

When the installation is done you can easily retrieve Analytics data. Mostly all methods will return an `Illuminate\Support\Collection`-instance.


Here is an example to retrieve visitors and pageview data for the last seven days.
```php
/*
* $analyticsData now contains a Collection with 3 columns: "date", "visitors" and "pageViews"
*/
$analyticsData = LaravelAnalytics::getVisitorsAndPageViews(7);
```

Here's another example to get the 20 most visited pages of the last 365 days
```php
/*
* $analyticsData now contains a Collection with 2 columns: "url" and "pageViews"
*/
$analyticsData = LaravelAnalytics::getMostVisitedPages(365, 20);
```
## Provided methods

###Visitors and pageviews
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

###Currently active visitors
This method uses the [Real Time Reporting API](https://developers.google.com/analytics/devguides/reporting/realtime/v3/). It returns the amount of visitors that is viewing your site
right now.
```php
    /**
     * Get the number of active users currently on the site
     *
     */
    public function getActiveUsers()
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

###Manually setting the siteId at runtime.
You can also set the siteId at runtime. This can be useful if you need to get statistics from multiple sites in one project.
```php
 LaravelAnalytics::setSiteId('ga:12345')->getVisitorsAndPageViews(); // will use the given siteId
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
