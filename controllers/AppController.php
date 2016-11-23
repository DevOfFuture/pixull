<?php

class AppController {
	static function index () {
		if (AppUser::is_signed_in()) {
			// app user is not signed in, send them to the dashboard
			Redirector::redirect('dashboard');
		}
		else {
			// app user is not signed in, send them to sign in form
			Redirector::redirect('sign_in');
		}
	}

	static function four_zero_four () {
		// show the 404 page
		Viewer::view('app_404');
	}

	static function sign_in () {
		if (Inputter::has_values()) {
			// app user is attempting to sign in
			Validator::set_demo_allowed(true);
			Validator::set_rule('email_address', 'Email Address', Inputter::get_desanitized('email_address'), ['required', 'email_address']);
			Validator::set_rule('password', 'Password', Inputter::get_raw('password'), ['required']);

			if (Validator::valid()) {
				// ensure user is not locked out from sign in action
				if (!AppUser::is_locked_out('sign_in', '15 minutes', 3)) {
					// ensure user credentials are valid
					if (AppUser::credentials_valid()) {
						// user credentials are valid, sign them in
						AppUser::sign_in();
						Redirector::redirect('dashboard');
					}
					else {
						// user credentials are invalid, show error
						AppUser::create_attempt('sign_in');
						Alerter::set_message('error', 'Invalid email address or password entered');
					}
				}
				else {
					// user is locked out due to too many failed sign in attempts
					Alerter::set_message('error', 'Locked out due to too many failed attempts');
				}
			}
		}

		// show the sign in form
		Viewer::view('app_sign_in');
	}

	static function forgot_password () {
		if (Inputter::has_values()) {
			// user is attempting to retrieve password reset link
			Validator::set_rule('email_address', 'Email Address', Inputter::get_desanitized('email_address'), ['required', 'email_address']);

			if (Validator::valid()) {
				// ensure user is not locked out from forgot password action
				if (!AppUser::is_locked_out('forgot_password', '15 minutes', 3)) {
					// user is not locked out from submitting password reset link request
					AppUser::create_attempt('forgot_password');
					AppUser::send_password_reset_link();
					Alerter::set_message('success', 'If the email address entered exists, a password reset link will be sent to it');
					Inputter::set_empty_properties();
				}
				else {
					// user is trying to send too many password reset emails and is locked out
					Alerter::set_message('error', 'Locked out due to too many requests');
				}
			}
		}

		// show the forgot password form
		Viewer::view('app_forgot_password');
	}

	static function reset_password ($token, $email_address) {
		// ensure token exists and has not expired
		if (!AppUser::password_reset_token_valid($token, $email_address)) {
			Redirector::redirect('sign_in');
		}

		if (Inputter::has_values()) {
			// user is attempting to reset their password
			Validator::set_rule('new_password', 'New Password', Inputter::get_raw('new_password'), ['required', 'min_length' => 6]);

			if (Validator::valid()) {
				AppUser::change_password(User::get_id_by_email_address($email_address));
				Alerter::set_message('success', 'Your password has been reset successfully');
				Redirector::redirect('sign_in');
			}
		}

		// show the reset password form
		Viewer::view('app_reset_password');
	}

	static function dashboard () {
		// show the app dashboard
		Viewer::view('app_dashboard');
	}

	static function update_profile () {
		if (Inputter::has_value('update_details')) {
			// user is attempting to update details
			Validator::set_rule('email_address', 'Email Address', Inputter::get_desanitized('email_address'), ['required', 'email_address', 'not_in_db' => ['user', 'email_address', AppUser::get_email_address()]]);
			Validator::set_rule('timezone', 'Timezone', Inputter::get_desanitized('timezone'), ['required', 'in_array' => timezone_identifiers_list()]);

			if (Validator::valid()) {
				// email address changed, remove all tokens except current
				if (Inputter::get_desanitized('email_address') != AppUser::get_email_address()) {
					AppUser::delete_tokens(AppUser::get_id(), AppUser::get_token());
				}

				// update details and redirect to same page so the form is updated
				AppUser::update_details(AppUser::get_id(), 'partial');
				Alerter::set_message('success', 'Your details have been updated successfully');
				Redirector::redirect('update_profile');
			}
		}
		else if (Inputter::has_value('change_password')) {
			// user is attempting to change password
			Validator::set_rule('current_password', 'Current Password', Inputter::get_raw('current_password'), ['required']);
			Validator::set_rule('new_password', 'New Password', Inputter::get_raw('new_password'), ['required', 'min_length' => 6]);

			if (Validator::valid()) {
				// ensure current password is valid
				if (AppUser::current_password_valid()) {
					// current password valid, remove all tokens except current
					AppUser::delete_tokens(AppUser::get_id(), AppUser::get_token());

					// change their password to new password
					AppUser::change_password(AppUser::get_id());
					Alerter::set_message('success', 'Your password has been changed successfully');
				}
				else {
					// current password entered is invalid
					Alerter::set_message('error', 'Invalid current password entered');
				}
			}
		}

		// show the update profile form
		Viewer::view('app_update_profile');
	}

	static function sign_out () {
		// sign the user out
		AppUser::sign_out();

		// send them to the sign in form
		Redirector::redirect('sign_in');
	}
}