<?php

namespace Spatie\Analytics\Exceptions;

use Exception;

class InvalidFilter extends Exception
{
    public static function filterMustContainGA($filter)
    {
        return new static("Filter `{$filter}` does not contain ga:");
    }
}
