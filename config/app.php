<?php

if (APP_ENVIRONMENT == 'development') {
	// development app settings
	define('APP_URL', 'http://localhost/pixull');
	define('APP_DEMO_MODE', false);
}
else if (APP_ENVIRONMENT == 'production') {
	// production app settings
	define('APP_URL', 'http://pixull.com/demo');
	define('APP_DEMO_MODE', false);
}

// non-environment based app settings
define('APP_TITLE', 'Pixull');
define('APP_TITLE_SHORT', 'P');
define('APP_VERSION', '1.0.0');
define('APP_DATE_FORMAT', 'm/d/Y g:i A');
define('APP_DEFAULT_TIMEZONE', 'America/Toronto');
define('APP_REMEMBER_TIME', '1 year');
define('APP_PASSWORD_RESET_EXPIRE', '24 hours');

// define all possible app user permissions below
define('APP_PERMISSIONS', serialize([
	['Create Users', 'Read Users', 'Update Users', 'Delete Users'],
	// generator hook (do not remove or modify this comment)
]));