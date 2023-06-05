<?php

namespace Botble\Analytics\GA4\Traits;

trait RowOperationTrait
{
    public bool|null $keepEmptyRows = null;

    public int|null $limit = null;

    public int|null $offset = null;

    public function keepEmptyRows(bool $keepEmptyRows = false): self
    {
        $this->keepEmptyRows = $keepEmptyRows;

        return $this;
    }

    public function limit(int $limit = null): self
    {
        $this->limit = $limit;

        return $this;
    }

    public function offset(int $offset = null): self
    {
        $this->offset = $offset;

        return $this;
    }
}
