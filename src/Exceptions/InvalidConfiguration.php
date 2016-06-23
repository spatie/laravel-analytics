<?php

namespace Spatie\Analytics\Exceptions;

use Exception;

class InvalidConfiguration extends Exception {
	public static function viewIdNotSpecified() {
		return new static('You must provide a valid view id.');
	}

	public static function credentialsJsonDoesNotExist($path) {
		return new static("Could not find a credentials file at `{$path}`.");
	}
}
