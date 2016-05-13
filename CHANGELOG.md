# Changelog

All Notable changes to Laravel-Analytics will be documented in this file

## 1.4.1 - 2015-05-12

- fixes a bug introduced in 1.4.0 where the check whether a p12 certificate is present was broken 

## 1.4.0 - 2015-05-11

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
