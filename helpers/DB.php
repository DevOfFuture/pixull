<?php

class DB {
	/** @var $pdo PDO */
	private static $pdo;

	static function initialize () {
		self::set_pdo();
	}

	static function set_pdo () {
		// set PDO object for database interactions
		self::$pdo = new PDO(APP_DATABASE_DRIVER.':host='.APP_DATABASE_HOST.';port='.APP_DATABASE_PORT.';dbname='.APP_DATABASE_NAME, APP_DATABASE_USERNAME, APP_DATABASE_PASSWORD);
		self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		self::$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
	}

	static function get_pdo () {
		// return PDO object
		return self::$pdo;
	}
}