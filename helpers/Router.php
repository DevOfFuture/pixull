<?php

class Router {
	private static $routes = [];

	static function set_route ($regex_path, $controller_method, $auth_required = 'any', $permission_required = null) {
		// set a route by parameters
		self::$routes[] = [
			'regex_path' => $regex_path,
			'controller_method' => $controller_method,
			'auth_required' => $auth_required,
			'permission_required' => $permission_required
		];
	}

	static function route () {
		// GET the current URL path, use app index if not set
		if (isset($_GET['path'])) {
			$get_path = $_GET['path'];
		}
		else {
			$get_path = '/';
		}

		// check if GET path has a match, show 404 by default if it does not
		$parameters = [];
		$controller_method = 'AppController::four_zero_four';

		foreach (self::$routes as $route) {
			if ($route['regex_path'] != '/') {
				$route['regex_path'] = trim($route['regex_path'], '/');
			}

			if (preg_match('|^'.$route['regex_path'].'$|i', $get_path, $parameters)) {
				// match found for GET path
				if ($route['auth_required'] == 'not_signed_in' && AppUser::is_signed_in()) {
					// auth required is not_signed_in and user is signed in, redirect to dashboard
					Redirector::redirect('dashboard');
				}
				else if ($route['auth_required'] == 'signed_in' && !AppUser::is_signed_in()) {
					// auth required is signed in and user is not signed in, redirect to sign in page
					Redirector::redirect('sign_in');
				}

				if ($route['permission_required'] == null || AppUser::has_permission($route['permission_required'])) {
					// set controller method if user has permission
					$controller_method = $route['controller_method'];
				}
				else {
					// user does not have permission, redirect to index
					Redirector::redirect('/');
				}

				// remove first parameter as it is the GET path and not a value we want
				if (!empty($parameters)) {
					unset($parameters[0]);
				}

				break;
			}
		}

		// route to the corresponding controller method
		call_user_func_array($controller_method, $parameters);
	}
}