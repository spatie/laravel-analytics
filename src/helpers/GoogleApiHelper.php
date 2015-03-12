<?php namespace Spatie\LaravelAnalytics\Helpers;

use Google_Client;
use Google_Service_Analytics;

class GoogleApiHelper {

    protected $client;
    protected $service;
    protected $siteIds = [];

    public function __construct(Google_Client $client)
    {
        $this->client = $client;
        $this->service = new Google_Service_Analytics($client);
    }

    public function query($id, $startDate, $endDate, $metrics, $others = [])
    {
        return $this->service->data_ga->get($id, $startDate, $endDate, $metrics, $others);
    }

    public function getSiteIdByUrl($url)
    {
        static $siteIds = null;

        if (is_null($siteIds))
        {
            $siteIds = $this->getSiteIds();
        }


        if (isset($this->siteIds[$url])) {
            return $this->siteIds[$url];
        }

        throw new \Exception("Site $url is not present in your Analytics account.");
    }

    public function getAllSiteIds()
    {
        if (empty($this->siteIds)) {
            $sites = $this->service->management_profiles->listManagementProfiles("~all", "~all");
            foreach($sites['items'] as $site) {
                $this->siteIds[$site['websiteUrl']] = 'ga:' . $site['id'];
            }
        }

        return $this->siteIds;
    }

    /**
     * @return mixed
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * @return array
     */
    public function getSiteIds()
    {
        return $this->siteIds;
    }

    /**
     * @param $siteIds
     */
    public function setSiteIds($siteIds)
    {
        $this->siteIds = $siteIds;
    }

    /**
     * @return mixed
     */
    public function getService()
    {
        return $this->service;
    }
}