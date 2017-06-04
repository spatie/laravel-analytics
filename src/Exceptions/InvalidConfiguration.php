<?php

namespace Spatie\Analytics\Exceptions;

use Exception;

class InvalidConfiguration extends Exception
{
    public static function viewIdNotSpecified()
    {
        return new static('There was no view id specified. You must provide a valid view id to execute querys on Google Analytics.');
    }

    public static function credentialsJsonDoesNotExist(string $path)
    {
        return new static("Could not find a credentials file at `{$path}`.");
    }
}
