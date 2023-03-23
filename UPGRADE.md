# Upgrading to v5

From version 5 laravel-analytics will leverage the new Google Analytics 4 Data API.
Because of this rewrite a number of breaking changes were made. This guide will help you upgrade to the latest version.

## Installation

As mentioned v5 uses a different API so you will need to enable it on https://console.developers.google.com/apis. If you want to know more about this you can consult the installation instructions in the README.

## Method changes

### `fetchVisitorsAndPageViews`
will now return `activeUsers`, `screenPageViews` and `pageTitle`. The `date` property has been removed.
If you want to have the results grouped by day you can use the `fetchVisitorsAndPageViewsByDate` function.

### `fetchTotalVisitorsAndPageViews`
will now return `date`, `activeUsers` and `screenPageViews`.

### `fetchMostVisitedPages`
will now return `fullPageUrl`, `pageTitle` and `screenPageViews`.

### `fetchTopReferrers`
will now return `screenPageViews` and `pageReferrer`.

### `fetchTopBrowsers`
will now return `screenPageViews` and `browser`.

### `fetchUserTypes`
will return a collection with items that have `activeUsers` and `newVsReturning` which can equal to `new` or `returning`.
