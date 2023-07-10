# Changelog

All notable changes to Laravel-Analytics will be documented in this file

## 5.0.3 - 2023-07-10

### What's Changed

- Bump dependabot/fetch-metadata from 1.5.1 to 1.6.0 by @dependabot in https://github.com/spatie/laravel-analytics/pull/497
- Adds a $keepEmptyRows parameter to get the empty rows too. by @golchha21 in https://github.com/spatie/laravel-analytics/pull/494

### New Contributors

- @golchha21 made their first contribution in https://github.com/spatie/laravel-analytics/pull/494

**Full Changelog**: https://github.com/spatie/laravel-analytics/compare/5.0.2...5.0.3

## 5.0.2 - 2023-05-30

### What's Changed

- Bump aglipanci/laravel-pint-action from 0.1.0 to 2.2.0 by @dependabot in https://github.com/spatie/laravel-analytics/pull/477
- Bump dependabot/fetch-metadata from 1.4.0 to 1.5.0 by @dependabot in https://github.com/spatie/laravel-analytics/pull/488
- Bump dependabot/fetch-metadata from 1.5.0 to 1.5.1 by @dependabot in https://github.com/spatie/laravel-analytics/pull/489
- Bump aglipanci/laravel-pint-action from 2.2.0 to 2.3.0 by @dependabot in https://github.com/spatie/laravel-analytics/pull/490
- Extend Analytics::get with dimensionFilter parameter by @gmbenedek in https://github.com/spatie/laravel-analytics/pull/487

### New Contributors

- @gmbenedek made their first contribution in https://github.com/spatie/laravel-analytics/pull/487

**Full Changelog**: https://github.com/spatie/laravel-analytics/compare/5.0.1...5.0.2

## 5.0.1 - 2023-05-17

- Adds an `$offset` paramter to the get method and all methods that already accepted a `$limit` paramter.

## 5.0.0 - 2023-04-06

### What's Changed

- Analytics API v4 by @timvandijck in https://github.com/spatie/laravel-analytics/pull/475

### New Contributors

- @timvandijck made their first contribution in https://github.com/spatie/laravel-analytics/pull/475

**Full Changelog**: https://github.com/spatie/laravel-analytics/compare/4.1.1...5.0.0

## 4.1.1 - 2023-02-01

### What's Changed

- Updated instructions by @CaddyDz in https://github.com/spatie/laravel-analytics/pull/446
- Feature: Convert PHPUnit tests to PestPHP by @mansoorkhan96 in https://github.com/spatie/laravel-analytics/pull/456
- Feature: Betterify Tests by @mansoorkhan96 in https://github.com/spatie/laravel-analytics/pull/457
- Add PHP 8.2 Tests Support by @patinthehat in https://github.com/spatie/laravel-analytics/pull/465
- Enable Dependabot Automation by @patinthehat in https://github.com/spatie/laravel-analytics/pull/464
- Bump dependabot/fetch-metadata from 1.3.5 to 1.3.6 by @dependabot in https://github.com/spatie/laravel-analytics/pull/471
- Bump actions/checkout from 2 to 3 by @dependabot in https://github.com/spatie/laravel-analytics/pull/468
- Laravel 10.x Compatibility by @laravel-shift in https://github.com/spatie/laravel-analytics/pull/472

### New Contributors

- @dependabot made their first contribution in https://github.com/spatie/laravel-analytics/pull/471

**Full Changelog**: https://github.com/spatie/laravel-analytics/compare/4.1.0...4.1.1

## 4.1.0 - 2022-01-12

Allow Laravel 9

## 4.0.1 - 2021-06-10

- allow "Google_Service_Analytics_GaData" return type for performQuery methods (#402)

## 4.0.0 - 2021-03-26

- support PHP 8+
- drop support for PHP 7.x
- use PHP 8 syntax where possible

## 3.11.0 - 2021-03-04

- widened DateTime to DateTimeInterface to allow immutables usage (#390)

## 3.10.2 - 2020-11-28

- add support for PHP 8

## 3.10.1 - 2020-09-09

- Add support for Laravel 8

## 3.10.0 - 2020-06-26

- Create getter for $viewId in Analytics Class

## 3.9.0 - 2020-03-03

- add support for Laravel 7

## 3.8.1 - 2019-11-23

- allow symfony 5 components

## 3.8.0 - 2019-09-04

- add support for Laravel 6
- use Symfony's PSR-16 cache adapter

## 3.7.1 - 2019-03-05

- fix cache lifetime

## 3.7.0 - 2019-02-27

- drop support for Laravel 5.7 and lower
- drop support for PHP 7.1 and lower

## 3.6.3 - 2019-02-27

- add support for Laravel 5.8

## 3.6.2 - 2018-08-24

- add support for Laravel 5.7

## 3.6.1 - 2018-05-04

- fix infinite loop

## 3.6.0 - 2018-04-30

- add pagination to `performQuery`

## 3.5.0 - 2018-03-17

- `service_account_credentials_json` now also accepts an array

## 3.4.1 - 2018-02-08

- add compatibility with Laravel 5.6

## 3.4.0 - 2018-01-08

- allow dynamic modification of config

## 3.3.0 - 2017-11-03

- add `months` and `years` methods to `Period`

## 3.2.0 - 2017-10-30

- add `fetchUserTypes`

## 3.1.0 - 2017-08-31

- add compatibility with Laravel 5.5

## 3.0.1 - 2017-06-16

- make publishing the config file optional

## 3.0.0 - 2016-08-23

- add support for `v2` of the Google API
- renamed config file from `laravel-analytics.php` to `analytics.php`
- dropped support for anything lower than Laravel 5.4

## 2.4.0 - 2017-01-23

- add support for Laravel 5.4
- dropped support for anything lower than Laravel 5.3

## 2.3.1 - 2016-10-14

- improve exception message

## 2.3.0 - 2016-10-20

- added `fetchTotalVisitorsAndPageViews`

## 2.2.2 - 2016-08-23

- added L5.3 compatibility

## 2.2.1 - 2016-08-02

- added a fallback for the cache path setting

## 2.2.0 - 2016-07-23

- added config setting to specify cache path

## 2.1.0 - 2016-06-22

- added `pageTitle` to `fetchVisitorsAndPageViews` and `fetchMostVisitedPages`
- fixed `credentialsJsonDoesNotExist` exception

## 2.0.0 - 2016-06-20

- refactored all methods
- introduced `Spatie\Analytics\Period` to specify date ranges
- the package now uses json credentials instead of .p12 file
- the `Spatie\Analytics\Analtyics` class is much easier to extend

## 1.4.1 - 2016-05-12

- fixes a bug introduced in 1.4.0 where the check whether a p12 certificate is present was broken

## 1.4.0 - 2016-05-11

**This version does not work, please upgrade to 1.4.0**

- removing the use of facades

## 1.3.1

- make `calculateNumberOfDays`-function protected

## 1.3.0

- Added method getSiteId()

## 1.2.3

- Fix PHP 5.4 compatibility

## 1.2.2

- An injected `Spatie\LaravelAnalytics\LaravelAnalytics`-object will now be properly configured

## 1.2.1

- Removed a var_dump-call that was not supposed to be there

## 1.2.0

###Do not use this version as it contains a var_dump that will mess up your output

- Added a method to set the siteId at runtime

## 1.1.5

- Store Google Api's cache in Laravel's storage directory

## 1.1.4

- Handled a breaking change caused by Laravel 5.1

## 1.1.3

- Moved the repo

## 1.1.2

- Corrected a bug that caused an error when using the realtime-cache

## 1.1.1

- Corrected a bug that caused the realtime-cache to be stored for too long

## 1.1.0

- Added support for the [Real Time Reporting API](https://developers.google.com/analytics/devguides/reporting/realtime/v3/)

## 1.0.4

- Lowered minimum required Carbon version from 1.17 to ~1.0

## 1.0.3

- Fix the breaking error in the Api Helper.

## 1.0.2 (do not use this version)

- Lowered minimum required PHP version to 5.4
- This version is not working due to a bug in the Api Helper.

## 1.0.1

- Use Laraval 5 contracts to typehint cache
- Add DateTime type hints

## 1.0.0

- Stable first release

## 0.0.1

- Experimental initial release
