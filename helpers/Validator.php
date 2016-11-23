<?php

class Validator {
	private static $rules = [];
	private static $errors = [];
	private static $demo_allowed = false;

	static function set_rule ($name, $label, $value, $conditions, $custom_errors = []) {
		// set a rule using a name, label, value, and conditions
		self::$rules[] = [
			'name' => $name,
			'label' => $label,
			'value' => $value,
			'conditions' => $conditions,
			'custom_errors' => $custom_errors,
		];
	}

	static function set_demo_allowed ($demo_allowed) {
		// set demo allowed value
		self::$demo_allowed = $demo_allowed;
	}

	static function valid () {
		// rules considered valid until one fails
		$valid = true;

		if (APP_DEMO_MODE == true && !self::$demo_allowed) {
			// app is in demo mode and demo is not allowed
			$valid = false;
			Alerter::set_message('error', 'Not allowed in demo mode');
		}
		else {
			// ensure every rule is valid
			foreach (self::$rules as $rule) {
				if (!self::rule_valid($rule)) {
					$valid = false;
				}
			}

			if (!$valid) {
				// a rule failed, set generic error message for form correction
				Alerter::set_message('error', 'Please correct the errors below');
			}
		}

		return $valid;
	}

	static function rule_valid ($rule) {
		// check that each rule condition is valid
		foreach ($rule['conditions'] as $condition_key => $condition_value) {
			if (!self::condition_valid($rule['name'], $rule['label'], $rule['value'], $condition_key, $condition_value, $rule['custom_errors'])) {
				return false;
			}
		}

		return true;
	}

	static function condition_valid ($name, $label, $value, $condition_key, $condition_value, $custom_errors) {
		// no error by default
		$error = null;

		// validate conditions that have no parameters
		switch ($condition_value) {
			case 'required':
				// value can not be empty
				if (strlen($value) == 0) {
					$error = $label.' is required';
				}
				break;
			case 'alpha':
				// value must only contain alpha characters
				if (!ctype_alpha($value)) {
					$error = $label.' must only contain alpha characters';
				}
				break;
			case 'alphanumeric':
				// value must only contain alphanumeric characters
				if (!ctype_alnum($value)) {
					$error = $label.' must only contain alphanumeric characters';
				}
				break;
			case 'numeric':
				// value must only contain numeric characters
				if (!is_numeric($value)) {
					$error = $label.' must only contain numeric characters';
				}
				break;
			case 'integer':
				// value must be an integer
				if (!filter_var($value, FILTER_VALIDATE_INT)) {
					$error = $label.' must be an integer';
				}
				break;
			case 'boolean':
				// value must be boolean
				if (!filter_var($value, FILTER_VALIDATE_BOOLEAN)) {
					$error = $label.' must be boolean';
				}
				break;
			case 'email_address':
				// value must be a valid email address
				if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
					$error = $label.' must be a valid email address';
				}
				break;
			case 'url':
				// value must be a valid URL
				if (!filter_var($value, FILTER_VALIDATE_URL)) {
					$error = $label.' must be a valid URL';
				}
				break;
			case 'date':
				// value must be a date or time string
				if (!strtotime($value)) {
					$error = $label.' must be a date';
				}
				break;
		}

		// validate conditions that have parameters
		switch ($condition_key) {
			case 'min_length':
				// value must be at least {int} characters long
				if (strlen($value) < $condition_value) {
					$error = $label.' must be at least '.$condition_value.' characters long';
				}
				break;
			case 'max_length':
				// value must be {int} or less characters long
				if (strlen($value) > $condition_value) {
					$error = $label.' must be '.$condition_value.' or less characters long';
				}
				break;
			case 'min_number':
				// value must be greater than or equal to {int}
				if ($value < $condition_value) {
					$error = $label.' must be greater than or equal to '.$condition_value;
				}
				break;
			case 'max_number':
				// value must be less than or equal to {int}
				if ($value > $condition_value) {
					$error = $label.' must be less than or equal to '.$condition_value;
				}
				break;
			case 'regex':
				// value must match {regex}
				if (!preg_match($condition_value, $value)) {
					$error = $label.' is invalid';
				}
				break;
			case 'contains':
				// value must contain {str}
				if (stripos($value, $condition_value) === false) {
					$error = $label.' must contain '.$condition_value;
				}
				break;
			case 'doesnt_contain':
				// value must not contain {str}
				if (stripos($value, $condition_value) !== false) {
					$error = $label.' must not contain '.$condition_value;
				}
				break;
			case in_array($condition_key, ['in_list', 'not_in_list']):
				// check if value is in list comma-separated list or not
				$value = strtolower($value);
				$list = strtolower($condition_value);
				$list = explode(',', $list);
				$list = array_map('trim', $list);

				if ($condition_key == 'in_list' && !in_array($value, $list)) {
					// value must be in comma-separated list
					$error = $label.' is invalid';
				}
				else if ($condition_key == 'not_in_list' && in_array($value, $list)) {
					// value must not be in comma-separated list
					$error = $label.' is invalid';
				}
				break;
			case in_array($condition_key, ['in_array', 'not_in_array']):
				// check if value is in an array or not
				$value = strtolower($value);
				$array = array_map('strtolower', $condition_value);
				$array = array_map('trim', $array);

				if ($condition_key == 'in_array' && !in_array($value, $array)) {
					// value must be in array
					$error = $label.' is invalid';
				}
				else if ($condition_key == 'not_in_array' && in_array($value, $array)) {
					// value must not be in array
					$error = $label.' is invalid';
				}
				break;
			case in_array($condition_key, ['in_db', 'not_in_db']):
				// check if valuine is in database table column or not
				$table = $condition_value[0];
				$column = $condition_value[1];

				if (isset($condition_value[2])) {
					// exceptions are set
					$exceptions = $condition_value[2];

					$stm = DB::get_pdo()->prepare('select count(*) from '.$table.' where '.$column.'=:value and !find_in_set('.$column.', :exceptions)');
					$stm->bindParam(':exceptions', $exceptions);
				}
				else {
					// no exceptions set
					$stm = DB::get_pdo()->prepare('select count(*) from '.$table.' where '.$column.'=:value');
				}

				$stm->bindParam(':value', $value);
				$stm->execute();
				$res = $stm->fetchColumn();

				if ($condition_key == 'in_db' && $res == 0) {
					// value must be in database table column
					$error = $label.' does not exist';
				}
				else if ($condition_key == 'not_in_db' && $res > 0) {
					// value must not be in database table column
					$error = $label.' already exists';
				}
				break;
		}

		if ($error == null) {
			// condition is valid
			return true;
		}
		else {
			// if custom error message is set, overwrite default with it
			if (array_key_exists($condition_key, $custom_errors)) {
				$error = $custom_errors[$condition_key];
			}
			else if (!is_array($condition_value) && array_key_exists($condition_value, $custom_errors)) {
				$error = $custom_errors[$condition_value];
			}
			
			// condition is not valid, add to errors
			self::$errors[$name] = $error;
			return false;
		}
	}

	static function has_error ($name) {
		// check if an error exists by name
		if (array_key_exists($name, self::$errors)) {
			return true;
		}
		else {
			return false;
		}
	}

	static function get_error ($name) {
		// return the error message by name
		return self::$errors[$name];
	}
}