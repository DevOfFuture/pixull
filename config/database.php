<?php

if (APP_ENVIRONMENT == 'production') {
	// production database settings
	define('APP_DATABASE_DRIVER', '');
	define('APP_DATABASE_HOST', '');
	define('APP_DATABASE_PORT', '');
	define('APP_DATABASE_NAME', '');
	define('APP_DATABASE_USERNAME', '');
	define('APP_DATABASE_PASSWORD', '');
}
else if (APP_ENVIRONMENT == 'development') {
	// development database settings
	define('APP_DATABASE_DRIVER', 'mysql');
	define('APP_DATABASE_HOST', 'localhost');
	define('APP_DATABASE_PORT', '3306');
	define('APP_DATABASE_NAME', 'pixull');
	define('APP_DATABASE_USERNAME', 'root');
	define('APP_DATABASE_PASSWORD', '');
}