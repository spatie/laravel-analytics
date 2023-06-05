<?php

namespace Botble\Analytics\GA4\Traits;

use Botble\Analytics\GA4\AnalyticsResponse;
use Google\Analytics\Data\V1beta\RunReportResponse;

trait ResponseTrait
{
    public array $metricHeaders = [];
    public array $dimensionHeaders = [];

    private function formatResponse(RunReportResponse $response): AnalyticsResponse
    {
        $this->setDimensionAndMetricHeaders($response);

        return (new AnalyticsResponse())
            ->setGoogleResponse($response)
            ->setTable($this->getTable($response))
            ->setMetricAggregationsTable($this->getMetricAggregationsTable($response));
    }

    private function getMetricAggregationsTable(RunReportResponse $response): array
    {
        $output = [];

        $aggregationMethods = [
            'getTotals',
            'getMaximums',
            'getMinimums',
        ];

        foreach ($aggregationMethods as $aggregationMethod) {
            foreach ($response->{$aggregationMethod}() as $row) {
                if ($row->getMetricValues()->count()) {
                    $tableArray = [];
                    foreach ($row->getDimensionValues() as $key => $item) {
                        $tableArray[$key === 0 ? 'aggregation' : $this->dimensionHeaders[$key]] = $item->getValue();
                    }
                    foreach ($row->getMetricValues() as $key => $item) {
                        $tableArray[$this->metricHeaders[$key]] = $item->getValue();
                    }
                    $output[] = $tableArray;
                }
            }
        }

        return $output;
    }

    private function getTable(RunReportResponse $response): array
    {
        $output = [];

        foreach ($response->getRows() as $row) {
            $tableArray = [];
            foreach ($row->getDimensionValues() as $key => $item) {
                $tableArray[$this->dimensionHeaders[$key]] = $item->getValue();
            }
            foreach ($row->getMetricValues() as $key => $item) {
                $tableArray[$this->metricHeaders[$key]] = $item->getValue();
            }
            $output[] = $tableArray;
        }

        return $output;
    }

    private function setDimensionAndMetricHeaders(RunReportResponse $response): void
    {
        foreach ($response->getDimensionHeaders() as $header) {
            $this->dimensionHeaders[] = $header->getName();
        }

        foreach ($response->getMetricHeaders() as $header) {
            $this->metricHeaders[] = $header->getName();
        }
    }
}
