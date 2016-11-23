<?php

Viewer::set_param('title', 'Create User');
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
		<div class="col-md-12">
			<div class="box">
				<form method="post" role="form">
					<div class="box-body">
						<div class="form-group<?php if (Validator::has_error('username')): echo ' has-error'; endif; ?>">
							<label for="username">Username</label>
							<input name="username" id="username" type="text" class="form-control" value="<?php echo Inputter::get_sanitized('username'); ?>">
							<?php if (Validator::has_error('username')): echo '<span class="help-block">'.Validator::get_error('username').'</span>'; endif; ?>
						</div>
						<div class="form-group<?php if (Validator::has_error('email_address')): echo ' has-error'; endif; ?>">
							<label for="email_address">Email Address</label>
							<input name="email_address" id="email_address" type="text" class="form-control" value="<?php echo Inputter::get_sanitized('email_address'); ?>">
							<?php if (Validator::has_error('email_address')): echo '<span class="help-block">'.Validator::get_error('email_address').'</span>'; endif; ?>
						</div>
						<div class="form-group<?php if (Validator::has_error('password')): echo ' has-error'; endif; ?>">
							<label for="password">Password</label>
							<input name="password" id="password" type="password" class="form-control" value="<?php echo Inputter::get_sanitized('password'); ?>" autocomplete="new-password">
							<?php if (Validator::has_error('password')): echo '<span class="help-block">'.Validator::get_error('password').'</span>'; endif; ?>
						</div>
						<div class="form-group<?php if (Validator::has_error('timezone')): echo ' has-error'; endif; ?>">
							<label for="timezone">Timezone</label>
							<select name="timezone" id="timezone" class="form-control">
								<?php
								foreach (timezone_identifiers_list() as $timezone) {
									echo '<option value="'.$timezone.'"'.(($timezone == Inputter::get_desanitized('timezone', APP_DEFAULT_TIMEZONE)) ? ' selected' : '').'>'.$timezone.'</option>';
								}
								?>
							</select>
							<?php if (Validator::has_error('timezone')): echo '<span class="help-block">'.Validator::get_error('timezone').'</span>'; endif; ?>
						</div>
						<div class="form-group<?php if (Validator::has_error('level')): echo ' has-error'; endif; ?>">
							<label for="level">Level</label>
							<select name="level" id="level" class="form-control user-level">
								<option value=""></option>
								<?php
								$levels = ['Admin', 'Standard'];
								foreach ($levels as $level) {
									echo '<option value="'.$level.'"'.(($level == Inputter::get_desanitized('level')) ? ' selected' : '').'>'.$level.'</option>';
								}
								?>
							</select>
							<?php if (Validator::has_error('level')): echo '<span class="help-block">'.Validator::get_error('level').'</span>'; endif; ?>
						</div>
						<div class="form-group user-permissions<?php if (Inputter::get_desanitized('level') != 'Standard') echo ' collapse'; ?>">
							<b>Permissions</b> &nbsp; (<a href="#" class="select-all-user-permissions">select all</a> / <a href="#" class="deselect-all-user-permissions">deselect all</a>)
							<br>
							<?php
							$permissions = unserialize(APP_PERMISSIONS);
							foreach ($permissions as $key => $value) {
								foreach ($value as $permission) {
									echo '<label class="checkbox-inline"><input name="permissions[]" type="checkbox" value="'.$permission.'" '.((Inputter::has_value('permissions') && in_array($permission, Inputter::get_desanitized('permissions'))) ? 'checked' : '').'> '.$permission.'</label>';
								}
								echo '<br>';
							}
							?>
						</div>
					</div>

					<div class="box-footer">
						<button type="submit" class="btn btn-primary btn-flat">Create User</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</section>

<?php

Viewer::view('app_footer');