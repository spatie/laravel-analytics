<?php

use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Spatie\Analytics\Exceptions\InvalidConfiguration;

uses(\Spatie\Analytics\Tests\Integration\TestCase::class);

it('will_throw_an_exception_if_the_view_id_is_not_set', function () {
    config()->set('analytics.view_id', '');

    Analytics::fetchVisitorsAndPageViews(Carbon::now()->subDay(), Carbon::now());
})->throws(InvalidConfiguration::class);

it('allows_credentials_json_file', function () {
    Storage::fake('testing-storage');

    Storage::disk('testing-storage')
        ->put('test-credentials.json', json_encode(credentials()));

    $credentialsPath = storage_path('framework/testing/disks/testing-storage/test-credentials.json');

    config()->set('analytics.view_id', '123456');

    config()->set('analytics.service_account_credentials_json', $credentialsPath);

    $analytics = $this->app['laravel-analytics'];

    expect($analytics)->toBeInstanceOf(\Spatie\Analytics\AnalyticsLegacy::class);
});

it('will_throw_an_exception_if_the_credentials_json_does_not_exist', function () {
    config()->set('analytics.view_id', '123456');

    config()->set('analytics.service_account_credentials_json', 'bogus.json');

    Analytics::fetchVisitorsAndPageViews(Carbon::now()->subDay(), Carbon::now());
})->throws(InvalidConfiguration::class);

it('allows_credentials_json_to_be_array', function () {
    config()->set('analytics.view_id', '123456');

    config()->set('analytics.service_account_credentials_json', credentials());

    $analytics = $this->app['laravel-analytics'];

    expect($analytics)->toBeInstanceOf(\Spatie\Analytics\AnalyticsLegacy::class);
});

function credentials()
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
