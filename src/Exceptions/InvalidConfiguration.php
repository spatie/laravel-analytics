<?php

namespace Spatie\Analytics\Exceptions;

use Exception;

class InvalidConfiguration extends Exception
{
    public static function siteIdNotSpecified()
    {
        return new static("You must provide a valid site id");
    }

    public static function siteIdNotValid(string $invalidSiteId)
    {
        return new static("The provided value for site id `{$invalidSiteId}` is invalid. It should start with 'ga:'");
    }

    public static function clientSecretJsonFileDoesNotExist(string $path)
    {
        return new static("Could not find a credentials file at `{$path}`");
    }
}