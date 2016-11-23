// core Pixull JS

$(document).ready(function() {
	// populate each datatable which exists
	var datatable = $('[data-table]');

	if (datatable.length) {
		datatable.each(function () {
			$(this).DataTable( {
				serverSide: true,
				ajax: {
					url: $(this).data('table'),
					type: 'POST'
				},
				stateSave: true,
				columnDefs: [
					{
						targets: 'actions-column',
						className: 'actions-column',
						orderable: false
					}
				],
				scrollX: true
			});
		});
	}

	// confirm deletion of objects
	$(document).on('click', '.confirm-delete', function (event) {
		if (!confirm('Are you sure you want to delete this?') == true) {
			event.preventDefault();
		}
	});

	// when user level changes, show/hide permissions
	$(document).on('change', '.user-level', function () {
		if ($(this).val() == 'Standard') {
			$('.user-permissions').show();
		}
		else {
			$('.user-permissions').hide();
		}
	});

	// select all user permissions
	$(document).on('click', '.select-all-user-permissions', function (event) {
		event.preventDefault();

		$('.user-permissions').find(':checkbox').each(function () {
			$(this).prop('checked', true);
		});
	});

	// deselect all user permissions
	$(document).on('click', '.deselect-all-user-permissions', function (event) {
		event.preventDefault();

		$('.user-permissions').find(':checkbox').each(function () {
			$(this).prop('checked', false);
		});
	});

	// initialize bootstrap tooltips
	$('[data-toggle="tooltip"]').tooltip({
		container: 'body'
	});
});