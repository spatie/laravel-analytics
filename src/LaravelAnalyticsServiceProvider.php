<?php namespace Spatie\LaravelAnalytics;

use Illuminate\Support\ServiceProvider;

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
        if(!\File::exists(Config::get('analytics-reports::certificate_path')))
        {
            throw new \Exception("Can't find the .p12 certificate in: " . Config::get('analytics-reports::certificate_path'));
        }
    }

    protected function getGoogleCientConfig()
    {
        return [
            'oauth2_client_id' => Config::get('analytics-reports::client_id'),
            'use_objects' => Config::get('analytics-reports::use_objects'),
        ];
    }

    protected function configureCredentials(Google_Client $client)
    {
        $client->setAssertionCredentials(
            new \Google_Auth_AssertionCredentials(
                Config::get('analytics-reports::service_email'),
                ['https://www.googleapis.com/auth/analytics.readonly'],
                file_get_contents(Config::get('analytics-reports::certificate_path'))
            )
        );

        return $client;
    }
}