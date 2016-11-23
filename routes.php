<?php

// main app routes
Router::set_route('/', 'AppController::index');
Router::set_route('404', 'AppController::four_zero_four');
Router::set_route('sign_in', 'AppController::sign_in', 'not_signed_in');
Router::set_route('forgot_password', 'AppController::forgot_password', 'not_signed_in');
Router::set_route('reset_password/(.+)/(.+)', 'AppController::reset_password', 'not_signed_in');
Router::set_route('dashboard', 'AppController::dashboard', 'signed_in');
Router::set_route('update_profile', 'AppController::update_profile', 'signed_in');
Router::set_route('sign_out', 'AppController::sign_out', 'signed_in');

// user routes
Router::set_route('user', 'UserController::index', 'signed_in', 'Read Users');
Router::set_route('user/datatable', 'UserController::datatable', 'signed_in', 'Read Users');
Router::set_route('user/create', 'UserController::create', 'signed_in', 'Create Users');
Router::set_route('user/update/(\d+)', 'UserController::update', 'signed_in', 'Update Users');
Router::set_route('user/delete/(\d+)', 'UserController::delete', 'signed_in', 'Delete Users');

// generator hook (do not remove or modify this comment)

// route the app user based on the URL path
Router::route();