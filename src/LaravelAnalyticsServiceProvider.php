<?php namespace Spatie\LaravelAnalytics;

use Illuminate\Support\ServiceProvider;
use Google_Client;
use Config;
use Spatie\LaravelAnalytics\Helpers\GoogleApiHelper;

class LaravelAnalyticsServiceProvider extends ServiceProvider{

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/config/laravelanalytics.php' =>  config_path('laravelanalytics.php'),
        ], 'config');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('laravelanalytics', function($app)
        {
            $client = $this->getGoogleClient();

            $analyticsApi = new LaravelAnalytics($client, Config::get('laravelanalytics.siteId'), Config::get('laravelanalytics.cacheLifetime'));

            return $analyticsApi;
        });
    }

    protected function getGoogleClient()
    {

        $this->guardAgainstMissingP12();

        $config = $this->getGoogleCientConfig();

        $client = new Google_Client($config);

        $client->setAccessType('offline');

        $client = $this->configureCredentials($client);

        return new GoogleApiHelper($client);
    }

    protected function guardAgainstMissingP12()
    {
        if(!\File::exists(Config::get('laravelanalytics.certificate_path')))
        {
            throw new \Exception("Can't find the .p12 certificate in: " . Config::get('laravelanalytics.certificate_path'));
        }
    }

    protected function getGoogleCientConfig()
    {
        return [
            'oauth2_client_id' => Config::get('laravelanalytics.client_id'),
            'use_objects' => Config::get('laravelanalytics.use_objects'),
        ];
    }

    protected function configureCredentials(Google_Client $client)
    {
        $client->setAssertionCredentials(
            new \Google_Auth_AssertionCredentials(
                Config::get('laravelanalytics.service_email'),
                ['https://www.googleapis.com/auth/analytics.readonly'],
                file_get_contents(Config::get('laravelanalytics.certificate_path'))
            )
        );

        return $client;
    }
}