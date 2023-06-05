<?php

namespace Botble\Analytics\GA4\Traits;

use Google\Analytics\Data\V1beta\OrderBy;
use Google\Analytics\Data\V1beta\OrderBy\DimensionOrderBy;

trait OrderByDimensionTrait
{
    public function orderByDimension(string $name, string $order = 'ASC'): self
    {
        $dimensionOrderBy = (new DimensionOrderBy())
            ->setDimensionName($name);

        $this->orderBys[] = (new OrderBy())
            ->setDesc($order !== 'ASC')
            ->setDimension($dimensionOrderBy);

        return $this;
    }

    public function orderByDimensionDesc(string $name): self
    {
        return $this->orderByDimension($name, 'DESC');
    }
}
