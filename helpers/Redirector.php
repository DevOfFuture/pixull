<?php

class Redirector {
	static function redirect ($location) {
		// redirect URL
		if (filter_var($location, FILTER_VALIDATE_URL)) {
			// uRL is valid, use it as it is
			$url = $location;
		}
		else {
			// uRL is invalid, treat it as internal and prepend app url
			$url = APP_URL.'/'.$location;
		}

		// redirect app user to url
		header('Location: '.$url);

		// die() for security purposes
		die();
	}
}