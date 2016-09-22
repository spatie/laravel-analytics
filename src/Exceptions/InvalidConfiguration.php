<?php

namespace Spatie\Analytics\Exceptions;

use Exception;

class InvalidConfiguration extends Exception
{
    public static function viewIdNotSpecified()
    {
        return new static('You must provide a valid view id.');
    }
}
