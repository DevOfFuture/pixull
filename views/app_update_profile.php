<?php

Viewer::set_param('title', 'Update Profile');
Viewer::view('app_header');

?>

<section class="content-header">
	<h1><?php echo Viewer::get_param('title'); ?></h1>
</section>

<section class="content">
	<?php
	if (Alerter::has_message('success')) {
		echo '<div class="callout callout-success">'.Alerter::get_message('success').'</div>';
	}
	else if (Alerter::has_message('error')) {
		echo '<div class="callout callout-danger">'.Alerter::get_message('error').'</div>';
	}
	?>

	<div class="row">
		<div class="col-md-6">
			<div class="box">
				<div class="box-header with-border">
					<h3 class="box-title">Update Details</h3>
				</div>
				<form method="post" role="form">
					<div class="box-body">
						<div class="form-group<?php if (Validator::has_error('email_address')): echo ' has-error'; endif; ?>">
							<label for="email_address">Email Address</label>
							<input name="email_address" id="email_address" type="text" class="form-control" value="<?php echo Inputter::get_sanitized('email_address', AppUser::get_email_address()); ?>">
							<?php if (Validator::has_error('email_address')): echo '<span class="help-block">'.Validator::get_error('email_address').'</span>'; endif; ?>
						</div>
						<div class="form-group<?php if (Validator::has_error('timezone')): echo ' has-error'; endif; ?>">
							<label for="timezone">Timezone</label>
							<select name="timezone" id="timezone" class="form-control">
								<?php
								foreach (timezone_identifiers_list() as $timezone) {
									echo '<option value="'.$timezone.'"'.(($timezone == Inputter::get_desanitized('timezone', AppUser::get_timezone())) ? ' selected' : '').'>'.$timezone.'</option>';
								}
								?>
							</select>
							<?php if (Validator::has_error('timezone')): echo '<span class="help-block">'.Validator::get_error('timezone').'</span>'; endif; ?>
						</div>
					</div>

					<div class="box-footer">
						<button name="update_details" type="submit" class="btn btn-primary btn-flat">Update Details</button>
					</div>
				</form>
			</div>
		</div>

		<div class="col-md-6">
			<div class="box">
				<div class="box-header with-border">
					<h3 class="box-title">Change Password</h3>
				</div>
				<form method="post" role="form">
					<div class="box-body">
						<div class="form-group<?php if (Validator::has_error('current_password')): echo ' has-error'; endif; ?>">
							<label for="current_password">Current Password</label>
							<input name="current_password" id="current_password" type="password" class="form-control">
							<?php if (Validator::has_error('current_password')): echo '<span class="help-block">'.Validator::get_error('current_password').'</span>'; endif; ?>
						</div>
						<div class="form-group<?php if (Validator::has_error('new_password')): echo ' has-error'; endif; ?>">
							<label for="new_password">New Password</label>
							<input name="new_password" id="new_password" type="password" class="form-control">
							<?php if (Validator::has_error('new_password')): echo '<span class="help-block">'.Validator::get_error('new_password').'</span>'; endif; ?>
						</div>
					</div>

					<div class="box-footer">
						<button name="change_password" type="submit" class="btn btn-primary btn-flat">Change Password</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</section>

<?php

Viewer::view('app_footer');