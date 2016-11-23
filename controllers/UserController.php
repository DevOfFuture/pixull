<?php

class UserController {
	static function index () {
		// show user table
		Viewer::view('user_index');
	}

	static function datatable () {
		// show JSON-encoded datatable
		Viewer::view('user_datatable');
	}

	static function create () {
		if (Inputter::has_values()) {
			// attempting to create a user
			Validator::set_rule('username', 'Username', Inputter::get_desanitized('username'), ['required', 'alpha_numeric', 'not_in_db' => ['user', 'username']]);
			Validator::set_rule('email_address', 'Email Address', Inputter::get_desanitized('email_address'), ['required', 'email_address', 'not_in_db' => ['user', 'email_address']]);
			Validator::set_rule('password', 'Password', Inputter::get_raw('password'), ['required', 'min_length' => 6]);
			Validator::set_rule('timezone', 'Timezone', Inputter::get_desanitized('timezone'), ['required', 'in_array' => timezone_identifiers_list()]);
			Validator::set_rule('level', 'Level', Inputter::get_desanitized('level'), ['required', 'in_array' => ['Admin', 'Standard']]);

			if (Validator::valid()) {
				// create the user
				User::create();
				Alerter::set_message('success', 'User "'.Inputter::get_sanitized('username').'" created successfully');
				Inputter::set_empty_properties();
			}
		}

		// show user create form
		Viewer::view('user_create');
	}

	static function update ($id) {
		// ensure user exists by id
		if (User::exists_by_id($id)) {
			// user exists by id
			$user = User::get_by_id($id);

			if (Inputter::has_value('update_details')) {
				// attempting to update user details
				Validator::set_rule('username', 'Username', Inputter::get_desanitized('username'), ['required', 'alpha_numeric', 'not_in_db' => ['user', 'username', $user['username']]]);
				Validator::set_rule('email_address', 'Email Address', Inputter::get_desanitized('email_address'), ['required', 'email_address', 'not_in_db' => ['user', 'email_address', $user['email_address']]]);
				Validator::set_rule('timezone', 'Timezone', Inputter::get_desanitized('timezone'), ['required', 'in_array' => timezone_identifiers_list()]);
				Validator::set_rule('level', 'Level', Inputter::get_desanitized('level'), ['required', 'in_array' => ['Admin', 'Standard']]);

				if (Validator::valid()) {
					// email address changed
					if (Inputter::get_desanitized('email_address') != $user['email_address']) {
						if ($id == AppUser::get_id()) {
							// user being updated is current app user, delete all tokens except current
							User::delete_tokens($id, AppUser::get_token());
						}
						else {
							// user being updated is not current app user, delete all tokens
							User::delete_tokens($id);
						}
					}

					// update user and redirect to same page so the form is updated
					User::update_details($id);
					Alerter::set_message('success', 'User details have been updated successfully');
					Redirector::redirect('user/update/'.$id);
				}
			}
			else if (Inputter::has_value('change_password')) {
				// attempting to change user password
				Validator::set_rule('new_password', 'New Password', Inputter::get_raw('new_password'), ['required', 'min_length' => 6]);

				if (Validator::valid()) {
					if ($id == AppUser::get_id()) {
						// user being updated is current app user, delete all tokens except current
						User::delete_tokens($id, AppUser::get_token());
					}
					else {
						// user being updated is not current app user, delete all tokens
						User::delete_tokens($id);
					}

					// change their password to new password
					User::change_password($id);
					Alerter::set_message('success', 'User password has been changed successfully');
				}
			}

			// set param values and show view
			Viewer::set_param('user', $user);
			Viewer::view('user_update');
		}
		else {
			// user does not exist by id, redirect to user index
			Redirector::redirect('user');
		}
	}

	static function delete ($id) {
		// ensure user exists by id
		if (User::exists_by_id($id)) {
			// user exists by id, check if validator passed (stops demo mode)
			if (Validator::valid()) {
				// delete tokens and db entry
				User::delete_tokens($id);
				User::delete($id);

				if ($id == AppUser::get_id()) {
					// user deleted is current app user, redirect to sign in
					Redirector::redirect('sign_in');
				}
				else {
					// user deleted is not current app user, show success
					Alerter::set_message('success', 'User deleted successfully');
				}
			}
		}

		// redirect to user index
		Redirector::redirect('user');
	}
}