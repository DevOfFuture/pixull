<?php

class Sanitizer {
	private static $method;

	static function sanitize ($value) {
		// sanitize the value for safe output
		self::$method = 'sanitize_value';

		return self::get_value($value);
	}

	static function desanitize ($value) {
		// desanitize the value for database insertion
		self::$method = 'desanitize_value';

		return self::get_value($value);
	}

	static function get_value ($value) {
		if (is_array($value)) {
			// value is an array, call method recursively
			array_walk_recursive($value, function (&$item) {
				$item = call_user_func('self::'.self::$method, $item);
			});
		}
		else {
			// value is not an array, call method once
			$value = call_user_func('self::'.self::$method, $value);
		}

		return $value;
	}

	static function sanitize_value ($value) {
		// trim and convert special characters to entities
		return trim(htmlspecialchars($value, ENT_QUOTES));
	}

	static function desanitize_value ($value) {
		// trim and decode special characters
		return trim(htmlspecialchars_decode($value, ENT_QUOTES));
	}
}