<?php

class Alerter {
	static function set_message ($status, $message) {
		// set the alert message as a session variable
		$_SESSION['alert'][$status] = $message;
	}

	static function has_message ($status) {
		// check if an alert message exists by status
		if (isset($_SESSION['alert']) && array_key_exists($status, $_SESSION['alert'])) {
			return true;
		}
		else {
			return false;
		}
	}

	static function get_message ($status) {
		// get alert message by status
		$message = $_SESSION['alert'][$status];

		// unset alert message by status
		unset($_SESSION['alert'][$status]);

		// return the message
		return $message;
	}
}