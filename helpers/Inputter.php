<?php

class Inputter {
	private static $raw = [];
	private static $sanitized = [];
	private static $desanitized = [];

	static function set_properties ($array) {
		// set the properties of the inputter via an array (usually POST)
		self::$raw = $array;
		self::$sanitized = Sanitizer::sanitize($array);
		self::$desanitized = Sanitizer::desanitize($array);
	}

	static function set_empty_properties () {
		// empty class properties
		self::$raw = [];
		self::$sanitized = [];
		self::$desanitized = [];
	}

	static function get_raw ($key, $default = null) {
		// return the raw value by key
		return self::get_value($key, 'raw', $default);
	}

	static function get_sanitized ($key, $default = null) {
		// return the sanitized value by key for html output
		return self::get_value($key, 'sanitized', $default);
	}

	static function get_desanitized ($key, $default = null) {
		// return the desanitized value for DB interaction
		return self::get_value($key, 'desanitized', $default);
	}

	static function get_value ($key, $property, $default = null) {
		// ensure property key exists so it can be returned
		if (array_key_exists($key, self::${$property})) {
			return self::${$property}[$key];
		}
		else {
			return $default;
		}
	}

	static function has_values () {
		// check if this has any values
		if (!empty(self::$raw)) {
			return true;
		}
		else {
			return false;
		}
	}

	static function has_value ($key) {
		// check if this has a value by key
		if (isset(self::$raw[$key])) {
			return true;
		}
		else {
			return false;
		}
	}
}