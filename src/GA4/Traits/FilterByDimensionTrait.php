<?php

namespace Botble\Analytics\GA4\Traits;

use Google\Analytics\Data\V1beta\Filter;
use Google\Analytics\Data\V1beta\Filter\InListFilter;
use Google\Analytics\Data\V1beta\Filter\StringFilter;
use Google\Analytics\Data\V1beta\FilterExpression;

trait FilterByDimensionTrait
{
    public ?FilterExpression $dimensionFilter = null;

    public function whereDimension(string $name, int $matchType, $value, bool $caseSensitive = false): self
    {
        $stringFilter = (new StringFilter())->setCaseSensitive($caseSensitive)
            ->setMatchType($matchType)
            ->setValue($value);

        $filter = (new Filter())->setStringFilter($stringFilter)
            ->setFieldName($name);

        $this->dimensionFilter = (new FilterExpression())
            ->setFilter($filter);

        return $this;
    }

    public function whereDimensionIn(string $name, array $values, bool $caseSensitive = false): self
    {
        $inListFilter = (new InListFilter())->setCaseSensitive($caseSensitive)
            ->setValues($values);

        $filter = (new Filter())->setInListFilter($inListFilter)
            ->setFieldName($name);

        $this->dimensionFilter = (new FilterExpression())
            ->setFilter($filter);

        return $this;
    }
}
