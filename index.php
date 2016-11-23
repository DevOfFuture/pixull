<?php

// initialize PHP session
session_start();

// require app config files
require_once 'config/environment.php';
require_once 'config/app.php';
require_once 'config/database.php';
require_once 'config/mailer.php';

// autoload dependencies via composer
require_once 'vendor/autoload.php';

// initialize database connection
DB::initialize();

// initialize and set properties of app user
AppUser::initialize();

// set timezone based on app user
date_default_timezone_set(AppUser::get_timezone());

// set inputter properties if a form was posted
if (!empty($_POST)) {
	Inputter::set_properties($_POST);
}

// route the user to the matched controller method
include 'routes.php';