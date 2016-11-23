<?php

if (APP_ENVIRONMENT == 'development') {
	// development mailer settings
	define('APP_MAILER_PROTOCOL', 'SMTP');
	define('APP_MAILER_HOST', '');
	define('APP_MAILER_PORT', '587');
	define('APP_MAILER_FROM_ADDRESS', '');
	define('APP_MAILER_FROM_NAME', '');
	define('APP_MAILER_USERNAME', '');
	define('APP_MAILER_PASSWORD', '');
}
else if (APP_ENVIRONMENT == 'production') {
	// production mailer settings
	define('APP_MAILER_PROTOCOL', 'SMTP');
	define('APP_MAILER_HOST', '');
	define('APP_MAILER_PORT', '587');
	define('APP_MAILER_FROM_ADDRESS', '');
	define('APP_MAILER_FROM_NAME', '');
	define('APP_MAILER_USERNAME', '');
	define('APP_MAILER_PASSWORD', '');
}