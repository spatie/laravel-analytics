<?php

namespace Botble\Analytics\GA4\Traits;

use Google\Analytics\Data\V1beta\Filter;
use Google\Analytics\Data\V1beta\Filter\BetweenFilter;
use Google\Analytics\Data\V1beta\Filter\NumericFilter;
use Google\Analytics\Data\V1beta\FilterExpression;
use Google\Analytics\Data\V1beta\NumericValue;

trait FilterByMetricTrait
{
    public ?FilterExpression $metricFilter = null;

    public function whereMetric(string $name, int $operation, $value): self
    {
        $numericFilter = (new NumericFilter())
            ->setOperation($operation)
            ->setValue($this->getNumericObject($value));

        $filter = (new Filter())->setNumericFilter($numericFilter)
            ->setFieldName($name);

        $this->metricFilter = (new FilterExpression())
            ->setFilter($filter);

        return $this;
    }

    public function whereMetricBetween(string $name, $from, $to): self
    {
        $betweenFilter = (new BetweenFilter())->setFromValue($this->getNumericObject($from))
            ->setToValue($this->getNumericObject($to));

        $filter = (new Filter())->setBetweenFilter($betweenFilter)
            ->setFieldName($name);

        $this->metricFilter = (new FilterExpression())
            ->setFilter($filter);

        return $this;
    }

    private function getNumericObject($value): NumericValue
    {
        $numericValue = (new NumericValue());

        if (is_float($value)) {
            $numericValue->setDoubleValue($value);
        } else {
            $numericValue->setInt64Value($value);
        }

        return $numericValue;
    }
}
