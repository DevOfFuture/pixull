<?php

Viewer::set_param('title', 'Reset Password');
Viewer::view('app_header');

if (Alerter::has_message('error')) {
	echo '<div class="callout callout-danger">'.Alerter::get_message('error').'</div>';
}

?>

<form method="post">
	<div class="form-group has-feedback<?php if (Validator::has_error('new_password')): echo ' has-error'; endif; ?>">
		<input name="new_password" type="password" class="form-control" placeholder="New Password">
		<i class="fa fa-lock form-control-feedback"></i>
		<?php if (Validator::has_error('new_password')): echo '<span class="help-block">'.Validator::get_error('new_password').'</span>'; endif; ?>
	</div>
	<div class="form-group">
		<button type="submit" class="btn btn-primary btn-block btn-flat">Reset Password</button>
	</div>
</form>

<a href="<?php echo APP_URL; ?>/sign_in">I remembered my password</a>

<?php

Viewer::view('app_footer');