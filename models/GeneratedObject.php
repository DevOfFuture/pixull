<?php

// file used by generator, do not delete this unless you never use the object generator

class GeneratedObject {
	static function exists_by_id ($id) {
		// check if a Generated Object exists by id
		$stm = DB::get_pdo()->prepare('select count(*) from `generated_object` where `id`=:id');
		$stm->bindParam(':id', $id);
		$stm->execute();
		$res = $stm->fetchColumn();

		if ($res > 0) {
			// a Generated Object exists by id
			return true;
		}
		else {
			// no Generated Object exists by id
			return false;
		}
	}

	static function get_all () {
		// get all Generated Objects
		$stm = DB::get_pdo()->prepare('select * from `generated_object` order by `name` asc');
		$stm->execute();
		$res = $stm->fetchAll();

		// return sanitized array of values
		return Sanitizer::sanitize($res);
	}

	static function get_by_id ($id) {
		// get Generated Object data by id
		$stm = DB::get_pdo()->prepare('select * from `generated_object` where `id`=:id');
		$stm->bindParam(':id', $id);
		$stm->execute();
		$res = $stm->fetch();

		// return sanitized array of values
		return Sanitizer::sanitize($res);
	}

	static function get_datatable_array () {
		// set datatable columns
		$columns = [
			['db' => 'name', 'dt' => 0],
		];

		// set query variables
		$query_vars = Datatable::query_vars($columns);

		// get sanitized rows
		$stm = DB::get_pdo()->prepare("select * from `generated_object` {$query_vars['where']} {$query_vars['order']} {$query_vars['limit']}");
		$stm->execute(Datatable::bind_values($query_vars['bindings']));
		$rows = Sanitizer::sanitize($stm->fetchAll());

		// get filtered row count
		$stm = DB::get_pdo()->prepare("select count(*) from `generated_object` {$query_vars['where']}");
		$stm->execute(Datatable::bind_values($query_vars['bindings']));
		$filtered_count = $stm->fetchColumn();

		// get total row count
		$stm = DB::get_pdo()->prepare("select count(*) from `generated_object`");
		$stm->execute();
		$total_count = $stm->fetchColumn();

		// return array for use in view
		return Datatable::data_array($rows, $filtered_count, $total_count);
	}

	static function create () {
		// set values to be binded
		$name = Inputter::get_desanitized('name');

		// create Generated Object
		$stm = DB::get_pdo()->prepare('insert into `generated_object` (`name`) values (:name)');
		$stm->bindParam(':name', $name);
		$stm->execute();
	}

	static function update ($id) {
		// set variables to be binded
		$name = Inputter::get_desanitized('name');

		// update Generated Object
		$stm = DB::get_pdo()->prepare('update `generated_object` set `name`=:name where `id`=:id');
		$stm->bindParam(':name', $name);
		$stm->bindParam(':id', $id);
		$stm->execute();
	}

	static function delete ($id) {
		// delete Generated Object
		$stm = DB::get_pdo()->prepare('delete from `generated_object` where `id`=:id');
		$stm->bindParam(':id', $id);
		$stm->execute();
	}
}