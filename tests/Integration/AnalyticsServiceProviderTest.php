<?php

namespace Spatie\Analytics\Tests\Integration;

use Storage;
use Analytics;
use Carbon\Carbon;
use Spatie\Analytics\Exceptions\InvalidConfiguration;

class AnalyticsServiceProviderTest extends TestCase
{
    /** @test */
    public function it_will_throw_an_exception_if_the_view_id_is_not_set()
    {
        $this->app['config']->set('analytics.view_id', '');

        $this->expectException(InvalidConfiguration::class);

        Analytics::fetchVisitorsAndPageViews(Carbon::now()->subDay(), Carbon::now());
    }

    /** @test */
    public function it_allows_credentials_json_file()
    {
        Storage::fake('testing-storage');

        Storage::disk('testing-storage')
            ->put('test-credentials.json', json_encode($this->get_credentials()));

        $credentials_json_file_path = Storage::disk('testing-storage')
            ->getDriver()
            ->getAdapter()
            ->applyPathPrefix('test-credentials.json');

        $this->app['config']->set('analytics.view_id', '123456');

        $this->app['config']->set('analytics.service_account_credentials_json', $credentials_json_file_path);

        $analytics = $this->app['laravel-analytics'];

        $this->assertInstanceOf(\Spatie\Analytics\Analytics::class, $analytics);
    }

    /** @test */
    public function it_will_throw_an_exception_if_the_credentials_json_does_not_exist()
    {
        $this->app['config']->set('analytics.view_id', '123456');

        $this->app['config']->set('analytics.service_account_credentials_json', 'bogus.json');

        $this->expectException(InvalidConfiguration::class);

        Analytics::fetchVisitorsAndPageViews(Carbon::now()->subDay(), Carbon::now());
    }

    /** @test */
    public function it_allows_credentials_json_to_be_array()
    {
        $this->app['config']->set('analytics.view_id', '123456');

        $this->app['config']->set('analytics.service_account_credentials_json', $this->get_credentials());

        $analytics = $this->app['laravel-analytics'];

        $this->assertInstanceOf(\Spatie\Analytics\Analytics::class, $analytics);
    }

    protected function get_credentials()
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
}
