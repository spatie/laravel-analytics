<?php

return [
    /*
     * The site id is used to retrieve and display Google Analytics statistics
     * in the admin section.
     *
     * Should look like: ga:xxxxxxxx.
     */
    'site_id' => env('ANALYTICS_SITE_ID'),

    /*
     * Path to the client secret json file
     */
    'client_secret_json' => storage('app/laravel-google-analytics/client_secret.json'),

    /*
     * The amount of minutes the Google API responses will be cached.
     * If you set this to zero, the responses won't be cached at all.
     */
    'cacheLifetime' => 60 * 24,
];
