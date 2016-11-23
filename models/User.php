<?php

class User {
	public static $id;
	public static $username;
	public static $email_address;
	public static $timezone = APP_DEFAULT_TIMEZONE;
	public static $password_hash;
	public static $level;
	public static $permissions = [];

	static function exists_by_id ($id) {
		// check if a user exists by id
		$stm = DB::get_pdo()->prepare('select count(*) from `user` where `id`=:id');
		$stm->bindParam(':id', $id);
		$stm->execute();
		$res = $stm->fetchColumn();

		if ($res > 0) {
			// a user exists by id
			return true;
		}
		else {
			// no user exists by id
			return false;
		}
	}

	static function get_id () {
		// get user id
		return self::$id;
	}

	static function get_username () {
		// get username
		return self::$username;
	}

	static function get_email_address () {
		// get email address
		return self::$email_address;
	}

	static function get_timezone () {
		// get timezone
		return self::$timezone;
	}

	static function get_all () {
		// get all Generated Objects
		$stm = DB::get_pdo()->prepare('select * from `user` order by `username` asc');
		$stm->execute();
		$res = $stm->fetchAll();

		// return sanitized array of values
		return Sanitizer::sanitize($res);
	}

	static function get_by_id ($id) {
		// get user data by id
		$stm = DB::get_pdo()->prepare('select * from `user` where `id`=:id');
		$stm->bindParam(':id', $id);
		$stm->execute();
		$res = $stm->fetch();

		// unserialze permissions
		$res['permissions'] = unserialize($res['permissions']);

		// return sanitized array of values
		return Sanitizer::sanitize($res);
	}

	static function get_id_by_email_address ($email_address) {
		// get user ID by email address
		$stm = DB::get_pdo()->prepare('select `id` from `user` where `email_address`=:email_address');
		$stm->bindParam(':email_address', $email_address);
		$stm->execute();
		$res = $stm->fetchColumn();

		return $res;
	}

	static function get_datatable_array () {
		// set datatable columns
		$columns = [
			['db' => 'username', 'dt' => 0],
			['db' => 'email_address', 'dt' => 1],
			['db' => 'timezone', 'dt' => 2],
			['db' => 'level', 'dt' => 3],
		];

		// set query variables
		$query_vars = Datatable::query_vars($columns);

		// get sanitized rows
		$stm = DB::get_pdo()->prepare("select * from `user` {$query_vars['where']} {$query_vars['order']} {$query_vars['limit']}");
		$stm->execute(Datatable::bind_values($query_vars['bindings']));
		$rows = Sanitizer::sanitize($stm->fetchAll());

		// get filtered row count
		$stm = DB::get_pdo()->prepare("select count(*) from `user` {$query_vars['where']}");
		$stm->execute(Datatable::bind_values($query_vars['bindings']));
		$filtered_count = $stm->fetchColumn();

		// get total row count
		$stm = DB::get_pdo()->prepare("select count(*) from `user`");
		$stm->execute();
		$total_count = $stm->fetchColumn();

		// return array for use in view
		return Datatable::data_array($rows, $filtered_count, $total_count);
	}

	static function create () {
		// set values to be binded
		$username = Inputter::get_desanitized('username');
		$email_address = Inputter::get_desanitized('email_address');
		$password_hash = password_hash(Inputter::get_raw('password'), PASSWORD_DEFAULT);
		$timezone = Inputter::get_desanitized('timezone');
		$level = Inputter::get_desanitized('level');
		$permissions = serialize(Inputter::get_desanitized('permissions'));

		// create user
		$stm = DB::get_pdo()->prepare('insert into `user` (`username`, `email_address`, `password_hash`, `timezone`, `level`, `permissions`) values (:username, :email_address, :password_hash, :timezone, :level, :permissions)');
		$stm->bindParam(':username', $username);
		$stm->bindParam(':email_address', $email_address);
		$stm->bindParam(':password_hash', $password_hash);
		$stm->bindParam(':timezone', $timezone);
		$stm->bindParam(':level', $level);
		$stm->bindParam(':permissions', $permissions);
		$stm->execute();
	}

	static function update_details ($id, $details = 'full') {
		// set variables to be binded
		$email_address = Inputter::get_desanitized('email_address');
		$timezone = Inputter::get_desanitized('timezone');

		if ($details == 'full') {
			// update full user details (from user update page)
			$username = Inputter::get_desanitized('username');
			$level = Inputter::get_desanitized('level');
			$permissions = serialize(Inputter::get_desanitized('permissions'));

			$stm = DB::get_pdo()->prepare('update `user` set `username`=:username, `email_address`=:email_address, `timezone`=:timezone, `level`=:level, `permissions`=:permissions where `id`=:id');
			$stm->bindParam(':username', $username);
			$stm->bindParam(':level', $level);
			$stm->bindParam(':permissions', $permissions);
		}
		else {
			// update partial user details (from app user profile)
			$stm = DB::get_pdo()->prepare('update `user` set `email_address`=:email_address, `timezone`=:timezone where `id`=:id');
		}

		// update user
		$stm->bindParam(':email_address', $email_address);
		$stm->bindParam(':timezone', $timezone);
		$stm->bindParam(':id', $id);
		$stm->execute();
	}

	static function change_password ($id) {
		// hash new password
		$password_hash = password_hash(Inputter::get_raw('new_password'), PASSWORD_DEFAULT);

		// update user password
		$stm = DB::get_pdo()->prepare('update `user` set `password_hash`=:password_hash, `password_reset_token`=null, `password_reset_expire`=null where `id`=:id');
		$stm->bindParam(':password_hash', $password_hash);
		$stm->bindParam(':id', $id);
		$stm->execute();
	}

	static function delete ($id) {
		// delete user
		$stm = DB::get_pdo()->prepare('delete from `user` where `id`=:id');
		$stm->bindParam(':id', $id);
		$stm->execute();
	}

	static function delete_tokens ($user_id, $except_token = null) {
		if ($except_token != null) {
			// delete user tokens except specified one
			$stm = DB::get_pdo()->prepare('delete from `user_tokens` where `user_id`=:user_id and `token`!=:except_token');
			$stm->bindParam(':except_token', $except_token);
		}
		else {
			// delete all user tokens
			$stm = DB::get_pdo()->prepare('delete from `user_tokens` where `user_id`=:user_id');
		}

		$stm->bindParam(':user_id', $user_id);
		$stm->execute();
	}
}