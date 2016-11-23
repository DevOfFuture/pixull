<?php

// file used by generator, do not delete this unless you never use the object generator

class GeneratedObjectController {
	static function index () {
		// show Generated Object table
		Viewer::view('generated_object_index');
	}

	static function datatable () {
		// show JSON-encoded datatable
		Viewer::view('generated_object_datatable');
	}

	static function create () {
		if (Inputter::has_values()) {
			// attempting to create Generated Object
			Validator::set_rule('name', 'Name', Inputter::get_desanitized('name'), ['required', 'not_in_db' => ['generated_object', 'name']]);

			if (Validator::valid()) {
				// create Generated Object
				GeneratedObject::create();
				Alerter::set_message('success', 'Generated Object "'.Inputter::get_sanitized('name').'" created successfully');
				Inputter::set_empty_properties();
			}
		}

		// show Generated Object create form
		Viewer::view('generated_object_create');
	}

	static function update ($id) {
		// ensure Generated Object exists by id
		if (GeneratedObject::exists_by_id($id)) {
			// object exists by id
			$generated_object = GeneratedObject::get_by_id($id);

			if (Inputter::has_values()) {
				// attempting to update Generated Object details
				Validator::set_rule('name', 'Name', Inputter::get_desanitized('name'), ['required', 'not_in_db' => ['generated_object', 'name', $generated_object['name']]]);

				if (Validator::valid()) {
					// update Generated Object and redirect to same page so the form is updated
					GeneratedObject::update($id);
					Alerter::set_message('success', 'Generated Object has been updated successfully');
					Redirector::redirect('generated_object/update/' . $id);
				}
			}

			// set param values and show view
			Viewer::set_param('generated_object', $generated_object);
			Viewer::view('generated_object_update');
		}
		else {
			// object does not exist by id, redirect to object index
			Redirector::redirect('generated_object');
		}
	}

	static function delete ($id) {
		// ensure Generated Object exists by id
		if (GeneratedObject::exists_by_id($id)) {
			// generated Object exists by id, check if validator passed (stops demo mode)
			if (Validator::valid()) {
				// deleted Generated Object
				GeneratedObject::delete($id);
				Alerter::set_message('success', 'Generated Object deleted successfully');
			}
		}

		// redirect to Generated Object index
		Redirector::redirect('generated_object');
	}
}