<?php

// file used by generator, do not delete this unless you never use the object generator

Viewer::set_param('title', 'Create Generated Object');
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
						<div class="form-group<?php if (Validator::has_error('name')): echo ' has-error'; endif; ?>">
							<label for="name">Name</label>
							<input name="name" id="name" type="text" class="form-control" value="<?php echo Inputter::get_sanitized('name'); ?>">
							<?php if (Validator::has_error('name')): echo '<span class="help-block">'.Validator::get_error('name').'</span>'; endif; ?>
						</div>
					</div>

					<div class="box-footer">
						<button type="submit" class="btn btn-primary btn-flat">Create Generated Object</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</section>

<?php

Viewer::view('app_footer');