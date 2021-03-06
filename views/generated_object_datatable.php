<?php

// file used by generator, do not delete this unless you never use the object generator

// generate data output using data rows
$datatable_array = GeneratedObject::get_datatable_array();

foreach ($datatable_array['rows'] as $row) {
	// action control buttons
	$action_controls = [];
	if (AppUser::has_permission('Update Generated Objects')) $action_controls[] = '<a href="'.APP_URL.'/generated_object/update/'.$row['id'].'" class="btn btn-primary btn-flat" data-toggle="tooltip" title="Update"><i class="fa fa-pencil"></i></a>';
	if (AppUser::has_permission('Delete Generated Objects')) $action_controls[] = '<a href="' . APP_URL . '/generated_object/delete/' . $row['id'] . '" class="btn btn-danger btn-flat confirm-delete" data-toggle="tooltip" title="Delete"><i class="fa fa-trash"></i></a>';

	$datatable_array['data'][] = [
		$row['name'],
		implode(' ', $action_controls),
	];
}

// show JSON-encoded datatable object
echo json_encode($datatable_array);