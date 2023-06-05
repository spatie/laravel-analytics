<?php

namespace Botble\Analytics\GA4\Traits;

use Google\Analytics\Data\V1beta\OrderBy;
use Google\Analytics\Data\V1beta\OrderBy\MetricOrderBy;

trait OrderByMetricTrait
{
    public function orderByMetric(string $name, string $order = 'ASC'): self
    {
        $metricOrderBy = (new MetricOrderBy())
            ->setMetricName($name);

        $this->orderBys[] = (new OrderBy())
            ->setDesc($order !== 'ASC')
            ->setMetric($metricOrderBy);

        return $this;
    }

    public function orderByMetricDesc(string $name): self
    {
        return $this->orderByMetric($name, 'DESC');
    }
}
