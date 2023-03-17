<?php

use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Spatie\Analytics\Exceptions\InvalidConfiguration;

it('will throw an exception if the view id is not set', function () {
    config()->set('analytics.view_id', '');

    Analytics::fetchVisitorsAndPageViews(Carbon::now()->subDay(), Carbon::now());
})->throws(InvalidConfiguration::class);

it('allows credentials json file', function () {
    Storage::fake('testing-storage');

    Storage::disk('testing-storage')
        ->put('test-credentials.json', json_encode(credentials()));

    $credentialsPath = storage_path('framework/testing/disks/testing-storage/test-credentials.json');

    config()->set('analytics.view_id', '123456');

    config()->set('analytics.service_account_credentials_json', $credentialsPath);

    $analytics = $this->app['laravel-analytics'];

    expect($analytics)->toBeInstanceOf(\Spatie\Analytics\AnalyticsLegacy::class);
});

it('will throw an exception if the credentials json does not exist', function () {
    config()->set('analytics.view_id', '123456');

    config()->set('analytics.service_account_credentials_json', 'bogus.json');

    Analytics::fetchVisitorsAndPageViews(Carbon::now()->subDay(), Carbon::now());
})->throws(InvalidConfiguration::class);

it('allows credentials json to be array', function () {
    config()->set('analytics.view_id', '123456');

    config()->set('analytics.service_account_credentials_json', credentials());

    $analytics = $this->app['laravel-analytics'];

    expect($analytics)->toBeInstanceOf(\Spatie\Analytics\AnalyticsLegacy::class);
});

function credentials(): array
{
    return [
        'type' => 'service_account',
        'project_id' => 'bogus-project',
        'private_key_id' => 'bogus-id',
        'private_key' => 'bogus-key',
        'client_email' => 'bogus-user@bogus-app.iam.gserviceaccount.com',
        'client_id' => 'bogus-id',
        'auth_uri' => 'https://accounts.google.com/o/oauth2/auth',
        'token_uri' => 'https://accounts.google.com/o/oauth2/token',
        'auth_provider_x509_cert_url' => 'https://www.googleapis.com/oauth2/v1/certs',
        'client_x509_cert_url' => 'https://www.googleapis.com/robot/v1/metadata/x509/bogus-ser%40bogus-app.iam.gserviceaccount.com',
    ];
}
