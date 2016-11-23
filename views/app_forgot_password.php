<?php

Viewer::set_param('title', 'Forgot Password');
Viewer::view('app_header');

if (Alerter::has_message('success')) {
	echo '<div class="callout callout-success">'.Alerter::get_message('success').'</div>';
}
else if (Alerter::has_message('error')) {
	echo '<div class="callout callout-danger">'.Alerter::get_message('error').'</div>';
}

?>

<form method="post">
	<div class="form-group has-feedback<?php if (Validator::has_error('email_address')): echo ' has-error'; endif; ?>">
		<input name="email_address" type="text" class="form-control" placeholder="Email Address" value="<?php echo Inputter::get_sanitized('email_address'); ?>">
		<i class="fa fa-envelope form-control-feedback"></i>
		<?php if (Validator::has_error('email_address')): echo '<span class="help-block">'.Validator::get_error('email_address').'</span>'; endif; ?>
	</div>
	<div class="form-group">
		<button type="submit" class="btn btn-primary btn-block btn-flat">Send Password Reset Link</button>
	</div>
</form>

<a href="<?php echo APP_URL; ?>/sign_in">I remembered my password</a>

<?php

Viewer::view('app_footer');