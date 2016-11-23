<?php

// file used by generator, do not delete this unless you never use the object generator

Viewer::set_param('title', 'Generated Objects');
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

	<div class="box">
		<?php if (AppUser::has_permission('Create Generated Objects')): ?>
			<div class="box-header with-border">
				<a href="<?php echo APP_URL; ?>/generated_object/create" class="btn btn-primary btn-flat"><i class="fa fa-plus"></i> Create Generated Object</a>
			</div>
		<?php endif; ?>
		<div class="box-body">
			<div class="dataTables_wrapper form-inline dt-bootstrap">
				<table data-table="<?php echo APP_URL; ?>/generated_object/datatable" class="table table-bordered table-hover dataTable" role="grid" width="100%">
					<thead>
					<tr>
						<th>Name</th>
						<th class="actions-column">Actions</th>
					</tr>
					</thead>
				</table>
			</div>
		</div>
	</div>
</section>

<?php

Viewer::view('app_footer');