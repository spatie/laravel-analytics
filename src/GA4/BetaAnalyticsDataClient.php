<?php

namespace Botble\Analytics\GA4;

use Google\Analytics\Data\V1beta\Gapic\BetaAnalyticsDataGapicClient;
use Google\Analytics\Data\V1beta\RunReportRequest;
use Google\Analytics\Data\V1beta\RunReportResponse;
use Google\ApiCore\RequestParamsHeaderDescriptor;

class BetaAnalyticsDataClient extends BetaAnalyticsDataGapicClient
{
    use GapicClientTrait;

    protected static function getClientDefaults(): array
    {
        return [
            'serviceName' => self::SERVICE_NAME,
            'apiEndpoint' =>
                self::SERVICE_ADDRESS . ':' . self::DEFAULT_SERVICE_PORT,
            'clientConfig' =>
                __DIR__ .
                '/../../ga4/beta_analytics_data_client_config.json',
            'descriptorsConfigPath' =>
                __DIR__ .
                '/../../ga4/beta_analytics_data_descriptor_config.php',
            'gcpApiConfigPath' =>
                __DIR__ . '/../../ga4/beta_analytics_data_grpc_config.json',
            'credentialsConfig' => [
                'defaultScopes' => self::$serviceScopes,
            ],
            'transportConfig' => [
                'rest' => [
                    'restClientConfigPath' =>
                        __DIR__ .
                        '/../../ga4/beta_analytics_data_rest_client_config.php',
                ],
            ],
        ];
    }

    public function __construct(array $options = [])
    {
        $clientOptions = $this->buildClientOptions($options);
        $this->setClientOptions($clientOptions);
    }

    public function runReport(array $optionalArgs = [])
    {
        $request = new RunReportRequest();
        $requestParamHeaders = [];
        if (isset($optionalArgs['property'])) {
            $request->setProperty($optionalArgs['property']);
            $requestParamHeaders['property'] = $optionalArgs['property'];
        }

        if (isset($optionalArgs['dimensions'])) {
            $request->setDimensions($optionalArgs['dimensions']);
        }

        if (isset($optionalArgs['metrics'])) {
            $request->setMetrics($optionalArgs['metrics']);
        }

        if (isset($optionalArgs['dateRanges'])) {
            $request->setDateRanges($optionalArgs['dateRanges']);
        }

        if (isset($optionalArgs['dimensionFilter'])) {
            $request->setDimensionFilter($optionalArgs['dimensionFilter']);
        }

        if (isset($optionalArgs['metricFilter'])) {
            $request->setMetricFilter($optionalArgs['metricFilter']);
        }

        if (isset($optionalArgs['offset'])) {
            $request->setOffset($optionalArgs['offset']);
        }

        if (isset($optionalArgs['limit'])) {
            $request->setLimit($optionalArgs['limit']);
        }

        if (isset($optionalArgs['metricAggregations'])) {
            $request->setMetricAggregations(
                $optionalArgs['metricAggregations']
            );
        }

        if (isset($optionalArgs['orderBys'])) {
            $request->setOrderBys($optionalArgs['orderBys']);
        }

        if (isset($optionalArgs['currencyCode'])) {
            $request->setCurrencyCode($optionalArgs['currencyCode']);
        }

        if (isset($optionalArgs['cohortSpec'])) {
            $request->setCohortSpec($optionalArgs['cohortSpec']);
        }

        if (isset($optionalArgs['keepEmptyRows'])) {
            $request->setKeepEmptyRows($optionalArgs['keepEmptyRows']);
        }

        if (isset($optionalArgs['returnPropertyQuota'])) {
            $request->setReturnPropertyQuota(
                $optionalArgs['returnPropertyQuota']
            );
        }

        $requestParams = new RequestParamsHeaderDescriptor(
            $requestParamHeaders
        );
        $optionalArgs['headers'] = isset($optionalArgs['headers'])
            ? array_merge($requestParams->getHeader(), $optionalArgs['headers'])
            : $requestParams->getHeader();

        return $this->startCall(
            'RunReport',
            RunReportResponse::class,
            $optionalArgs,
            $request
        )->wait();
    }
}
