<?php

class Viewer {
	private static $params = [];

	static function set_param ($key, $value) {
		// set the page title
		self::$params[$key] = $value;
	}

	static function get_param ($key) {
		// ensure param key exists so it can be returned
		if (array_key_exists($key, self::$params)) {
			return self::$params[$key];
		}
		else {
			return null;
		}
	}

	static function view ($view) {
		// set view file path
		$view_file = 'views/'.$view.'.php';

		// include the view file if it exists, die with error if it does not
		if (file_exists($view_file)) {
			include $view_file;
		}
		else {
			die('View file '.$view_file.' does not exist');
		}
	}
}