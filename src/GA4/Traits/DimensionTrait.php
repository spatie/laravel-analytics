<?php

namespace Botble\Analytics\GA4\Traits;

use Google\Analytics\Data\V1beta\Dimension;

trait DimensionTrait
{
    public array $dimensions = [];

    public function dimension(string $name): self
    {
        $this->dimensions[] = (new Dimension())
            ->setName($name);

        return $this;
    }

    public function dimensions(string ...$items): self
    {
        $this->dimensions = [];

        foreach ($items as $item) {
            $this->dimension($item);
        }

        return $this;
    }
}
