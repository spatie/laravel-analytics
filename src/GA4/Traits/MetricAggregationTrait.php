<?php

namespace Botble\Analytics\GA4\Traits;

trait MetricAggregationTrait
{
    public array $metricAggregations = [];

    public function metricAggregation(int $value): self
    {
        $this->metricAggregations[] = $value;

        return $this;
    }

    public function metricAggregations(int ...$items): self
    {
        foreach ($items as $item) {
            $this->metricAggregation($item);
        }

        return $this;
    }
}
