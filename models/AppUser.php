<?php

class AppUser extends User {
	private static $token;
	private static $signed_in = false;
	
	static function initialize () {
		// user token variables
		$user_id = null;
		$token = null;

		// determine whether to use COOKIE or SESSION values
		if (isset($_COOKIE['user_id']) && isset($_COOKIE['token'])) {
			$user_id = $_COOKIE['user_id'];
			$token = $_COOKIE['token'];
		}
		else if (isset($_SESSION['user_id']) && isset($_SESSION['token'])) {
			$user_id = $_SESSION['user_id'];
			$token = $_SESSION['token'];
		}

		if (!empty($user_id) && !empty($token)) {
			// ensure user token exists
			$stm = DB::get_pdo()->prepare('select count(*) from `user_tokens` where `user_id`=:user_id and `token`=:token');
			$stm->bindParam(':user_id', $user_id);
			$stm->bindParam(':token', $token);
			$stm->execute();
			$res = $stm->fetchColumn();

			if ($res > 0) {
				// token exists, ensure user exists by id
				$stm = DB::get_pdo()->prepare('select * from `user` where `id`=:user_id');
				$stm->bindParam(':user_id', $user_id);
				$stm->execute();
				$res = $stm->fetch();

				if ($res) {
					// user exists by id, populate properties used by app
					self::$id = $res['id'];
					self::$username = Sanitizer::sanitize($res['username']);
					self::$email_address = Sanitizer::sanitize($res['email_address']);
					self::$timezone = Sanitizer::sanitize($res['timezone']);
					self::$password_hash = $res['password_hash'];
					self::$token = $token;
					self::$level = Sanitizer::sanitize($res['level']);
					self::$permissions = Sanitizer::sanitize(unserialize($res['permissions']));
					self::$signed_in = true;
				}
			}
		}
	}

	static function get_token () {
		// get token
		return self::$token;
	}

	static function is_signed_in () {
		// check if the app user is not signed in or not
		return self::$signed_in;
	}

	static function is_locked_out ($action, $lockout_time, $allowed_attempts) {
		// set lockout time to be checked against
		$lockout_time = strtotime('-'.$lockout_time);

		// check if the user is locked out from performing an action
		$stm = DB::get_pdo()->prepare('select count(*) from `user_attempts` where `action`=:action and `ip`=:ip and `date_attempted` > :lockout_time');
		$stm->bindParam(':action', $action);
		$stm->bindParam(':ip', $_SERVER['REMOTE_ADDR']);
		$stm->bindParam(':lockout_time', $lockout_time);
		$stm->execute();
		$res = $stm->fetchColumn();

		if ($res >= $allowed_attempts) {
			// user has failed action too many times within lockout time
			return true;
		}
		else {
			// user is allowed more attempts at the action
			return false;
		}
	}

	static function credentials_valid () {
		// attempt to select password hash by email address
		$email_address = Inputter::get_desanitized('email_address');

		$stm = DB::get_pdo()->prepare('select `password_hash` from `user` where `email_address`=:email_address');
		$stm->bindParam(':email_address', $email_address);
		$stm->execute();
		$res = $stm->fetchColumn();

		if ($res) {
			// email address is valid
			if (password_verify(Inputter::get_raw('password'), $res)) {
				// password is valid
				return true;
			}
		}

		// username or password invalid
		return false;
	}

	static function create_attempt ($action) {
		// log the action attempt
		$date_attempted = time();

		$stm = DB::get_pdo()->prepare('insert into `user_attempts` (`action`, `ip`, `date_attempted`) values (:action, :ip, :date_attempted)');
		$stm->bindParam(':action', $action);
		$stm->bindParam(':ip', $_SERVER['REMOTE_ADDR']);
		$stm->bindParam(':date_attempted', $date_attempted);
		$stm->execute();
	}

	static function send_password_reset_link () {
		// check to ensure the email address exists for a user
		$email_address = Inputter::get_desanitized('email_address');

		$stm = DB::get_pdo()->prepare('select count(*) from `user` where `email_address`=:email_address');
		$stm->bindParam(':email_address', $email_address);
		$stm->execute();
		$res = $stm->fetchColumn();

		if ($res > 0) {
			// a user exists with the email address, set token & expiration time
			$password_reset_token = bin2hex(openssl_random_pseudo_bytes(16));
			$password_reset_expire = strtotime('+'.APP_PASSWORD_RESET_EXPIRE);

			$stm = DB::get_pdo()->prepare('update `user` set `password_reset_token`=:password_reset_token, `password_reset_expire`=:password_reset_expire where `email_address`=:email_address');
			$stm->bindParam(':password_reset_token', $password_reset_token);
			$stm->bindParam(':password_reset_expire', $password_reset_expire);
			$stm->bindParam(':email_address', $email_address);
			$stm->execute();

			// send them the password reset link containing token & email address
			$password_reset_link = APP_URL.'/reset_password/'.$password_reset_token.'/'.urlencode($email_address);

			Mailer::set_to($email_address);
			Mailer::set_subject('Password Reset Link');
			Mailer::set_body('Click here to reset your password: '.$password_reset_link);
			Mailer::send();
		}
	}

	static function password_reset_token_valid ($password_reset_token, $email_address) {
		// ensure token exists and hasn't expired
		$time = time();

		$stm = DB::get_pdo()->prepare('select count(*) from `user` where `email_address`=:email_address and `password_reset_token`=:password_reset_token and `password_reset_expire` > :time');
		$stm->bindParam(':email_address', $email_address);
		$stm->bindParam(':password_reset_token', $password_reset_token);
		$stm->bindParam(':time', $time);
		$stm->execute();
		$res = $stm->fetchColumn();

		if ($res > 0) {
			// reset token exists and hasn't expired
			return true;
		}
		else {
			// reset token does not exist or has expired
			return false;
		}
	}

	static function sign_in () {
		// set user ID & random token
		$email_address = Inputter::get_desanitized('email_address');

		$stm = DB::get_pdo()->prepare('select `id` from `user` where `email_address`=:email_address');
		$stm->bindParam(':email_address', $email_address);
		$stm->execute();
		$res = $stm->fetchColumn();

		$user_id = $res;
		$token = bin2hex(openssl_random_pseudo_bytes(16));

		// set cookie and session values
		if (Inputter::get_desanitized('remember_me')) {
			setcookie('user_id', $user_id, strtotime('+'.APP_REMEMBER_TIME), '/');
			setcookie('token', $token, strtotime('+'.APP_REMEMBER_TIME), '/');
		}

		$_SESSION['user_id'] = $user_id;
		$_SESSION['token'] = $token;

		// insert user token
		$stm = DB::get_pdo()->prepare('insert into `user_tokens` (`user_id`, `token`) values (:user_id, :token)');
		$stm->bindParam(':user_id', $user_id);
		$stm->bindParam(':token', $token);
		$stm->execute();

		// delete previous sign in & forgot password attempts
		$actions = 'sign_in,forgot_password';

		$stm = DB::get_pdo()->prepare('delete from `user_attempts` where find_in_set(`action`, :actions) and `ip`=:ip');
		$stm->bindParam(':actions', $actions);
		$stm->bindParam(':ip', $_SERVER['REMOTE_ADDR']);
		$stm->execute();
	}

	static function current_password_valid () {
		// check if current password entered is valid
		if (password_verify(Inputter::get_raw('current_password'), self::$password_hash)) {
			return true;
		}
		else {
			return false;
		}
	}

	static function sign_out () {
		// remove token
		$stm = DB::get_pdo()->prepare('delete from `user_tokens` where `user_id`=:user_id and `token`=:token');
		$stm->bindParam(':user_id', self::$id);
		$stm->bindParam(':token', self::$token);
		$stm->execute();

		// expire cookie values if set
		if (isset($_COOKIE['user_id']) && isset($_COOKIE['token'])) {
			setcookie('user_id', null, 1, '/');
			setcookie('token', null, 1, '/');
		}

		// unset session values
		unset($_SESSION['user_id']);
		unset($_SESSION['token']);
	}

	static function has_permission ($permission) {
		// check if the user has a specific permission
		if (self::$level == 'Admin') {
			// admins always have permission
			return true;
		}
		else {
			// standard users must have the permission
			if (is_array(self::$permissions)) {
				// check permission array
				return in_array($permission, self::$permissions);
			}
			else {
				// permission array is null, they have no permissions
				return false;
			}
		}
	}
}