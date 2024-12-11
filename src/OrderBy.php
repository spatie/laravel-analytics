<?php

namespace Spatie\Analytics;

use Google\Analytics\Data\V1beta\OrderBy as GoogleOrderBy;
use Google\Analytics\Data\V1beta\OrderBy\DimensionOrderBy;
use Google\Analytics\Data\V1beta\OrderBy\MetricOrderBy;

class OrderBy
{
    public static function dimension(string $dimension, bool $descending = false): GoogleOrderBy
    {
        $dimensionOrderBy = (new DimensionOrderBy)->setDimensionName($dimension);

        return (new GoogleOrderBy)->setDimension(
            $dimensionOrderBy,
        )->setDesc($descending);
    }

    public static function metric(string $metric, bool $descending = false): GoogleOrderBy
    {
        $metricOrderBy = (new MetricOrderBy)->setMetricName($metric);

        return (new GoogleOrderBy)->setMetric(
            $metricOrderBy,
        )->setDesc($descending);
    }
}
