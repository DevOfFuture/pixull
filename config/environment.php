<?php

if (isset($_SERVER['HTTP_HOST']) && strpos($_SERVER['HTTP_HOST'], 'pixull.com') !== false) {
	// app is hosted on pixull.com, it is in production environment
	define('APP_ENVIRONMENT', 'production');
}
else {
	// app is not hosted on pixull.com, it is in development environment
	define('APP_ENVIRONMENT', 'development');
}