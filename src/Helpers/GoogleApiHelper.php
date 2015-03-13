<?php namespace Spatie\LaravelAnalytics\helpers;

use Google_Client;
use Google_Service_Analytics;

class GoogleApiHelper
{
    protected $client;
    protected $service;
    protected $siteIds = [];

    public function __construct(Google_Client $client)
    {
        $this->client = $client;
        $this->service = new Google_Service_Analytics($client);
    }

    /**
     * Query the Google Analytics Service with given parameters.
     *
     * @param $id
     * @param $startDate
     * @param $endDate
     * @param $metrics
     * @param array $others
     *
     * @return \Google_Service_Analytics_GaData
     */
    public function query($id, $startDate, $endDate, $metrics, $others = [])
    {
        return $this->service->data_ga->get($id, $startDate, $endDate, $metrics, $others);
    }

    /**
     * Get a site Id by its URL.
     *
     * @param $url
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public function getSiteIdByUrl($url)
    {
        static $siteIds = null;

        if (is_null($siteIds)) {
            $siteIds = $this->getSiteIds();
        }

        if (isset($this->siteIds[$url])) {
            return $this->siteIds[$url];
        }

        throw new \Exception("Site ".$url." is not present in your Analytics account.");
    }

    /**
     * Get all siteIds ( set them if siteIds is empty).
     *
     * @return array
     */
    public function getAllSiteIds()
    {
        if (empty($this->siteIds)) {
            $sites = $this->service->management_profiles->listManagementProfiles("~all", "~all");

            foreach ($sites['items'] as $site) {
                $this->siteIds[$site['websiteUrl']] = 'ga:'.$site['id'];
            }
        }

        return $this->siteIds;
    }

    /**
     * Get the Google_Client object.
     *
     * @return mixed
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * Get siteIds property.
     *
     * @return array
     */
    public function getSiteIds()
    {
        return $this->siteIds;
    }

    /**
     * Set [] of siteIds property.
     *
     * @param $siteIds
     */
    public function setSiteIds($siteIds)
    {
        $this->siteIds = $siteIds;
    }

    /**
     * Get the service property.
     *
     * @return mixed
     */
    public function getService()
    {
        return $this->service;
    }
}
