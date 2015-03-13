<?php return

    [
        /*
        |--------------------------------------------------------------------------
        | Analytics reports site id
        |--------------------------------------------------------------------------
        |
        | This site id is used to retrieve and display Google Analytics statistics
        | in the admin-section. Should be something like ga:xxxxxxxx.
        |
        */

        'siteId' => '',

        /*
        |--------------------------------------------------------------------------
        | Cache Lifetime
        |--------------------------------------------------------------------------
        |
        | The amount of minutes the Google API responses will be cached.
        | If you set this to zero, the responses won't be cached at all.
        |
        */

        'cacheLifetime' => 60 * 24 * 2,

        /*
        |--------------------------------------------------------------------------
        | Client ID
        |--------------------------------------------------------------------------
        |
        | Set your client id, it should look like:
        | xxxxxxxxxxxx-xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx.apps.googleusercontent.com
        |
        */

        'client_id'        => 'xxxxxxxxxxxx-xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx.apps.googleusercontent.com',

        /*
        |--------------------------------------------------------------------------
        | Service Account Name
        |--------------------------------------------------------------------------
        |
        | Set your service account name, it should look like:
        | xxxxxxxxxxxx-xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx@developer.gserviceaccount.com
        |
        */

        'service_email'    => 'xxxxxxxxxxxx-xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx@developer.gserviceaccount.com',

        /*
        |--------------------------------------------------------------------------
        | Path to the .p12 certificate
        |--------------------------------------------------------------------------
        |
        | You need to download this from the Google API Console when the
        | service account was created.
        |
        | Make sure you keep your key.p12 file in a secure location, and isn't
        | readable by others.
        |
        */

        'certificate_path' => __DIR__.'/xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx-privatekey.p12',
    ];
