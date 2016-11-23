<?php

class Datatable {
	static function query_vars ($columns) {
		$query_vars = [];
		$query_vars['bindings'] = [];
		$query_vars['limit'] = self::limit();
		$query_vars['order'] = self::order($columns);
		$query_vars['where'] = self::filter($columns, $query_vars['bindings']);

		return $query_vars;
	}

	static function data_array ($rows, $filtered_count, $total_count) {
		$data_array = [];
		$data_array['draw'] = Inputter::get_raw('draw', 0);
		$data_array['recordsTotal'] = $total_count;
		$data_array['recordsFiltered'] = $filtered_count;
		$data_array['rows'] = $rows;
		$data_array['data'] = [];

		return $data_array;
	}

	static function limit () {
		$limit = '';
		if (Inputter::has_value('start') && Inputter::get_raw('length') != -1) {
			$limit = "LIMIT " . intval(Inputter::get_raw('start')) . ", " . intval(Inputter::get_raw('length'));
		}
		return $limit;
	}

	static function order ($columns) {
		$order = '';
		if (Inputter::has_value('order') && count(Inputter::get_raw('order'))) {
			$orderBy = array();
			$dtColumns = self::pluck($columns, 'dt');
			for ($i = 0, $ien = count(Inputter::get_raw('order')); $i < $ien; $i++) {
				// Convert the column index into the column data property
				$columnIdx = intval(Inputter::get_raw('order')[$i]['column']);
				$requestColumn = Inputter::get_raw('columns')[$columnIdx];
				$columnIdx = array_search($requestColumn['data'], $dtColumns);
				$column = $columns[$columnIdx];
				if ($requestColumn['orderable'] == 'true') {
					$dir = Inputter::get_raw('order')[$i]['dir'] === 'asc' ?
						'ASC' :
						'DESC';
					$orderBy[] = $column['db'] . ' ' . $dir;
				}
			}
			$order = 'ORDER BY ' . implode(', ', $orderBy);
		}
		return $order;
	}

	static function filter ($columns, &$bindings) {
		$globalSearch = array();
		$columnSearch = array();
		$dtColumns = self::pluck($columns, 'dt');
		if (Inputter::has_value('search') && Inputter::get_raw('search')['value'] != '') {
			$str = Inputter::get_raw('search')['value'];
			for ($i = 0, $ien = count(Inputter::get_raw('columns')); $i < $ien; $i++) {
				$requestColumn = Inputter::get_raw('columns')[$i];
				$columnIdx = array_search($requestColumn['data'], $dtColumns);
				$column = $columns[$columnIdx];
				if ($requestColumn['searchable'] == 'true') {
					$binding = self::bind($bindings, '%' . $str . '%', PDO::PARAM_STR);
					$globalSearch[] = $column['db'] . " LIKE " . $binding;
				}
			}
		}
		// Individual column filtering
		if (Inputter::has_value('columns')) {
			for ($i = 0, $ien = count(Inputter::get_raw('columns')); $i < $ien; $i++) {
				$requestColumn = Inputter::get_raw('columns')[$i];
				$columnIdx = array_search($requestColumn['data'], $dtColumns);
				$column = $columns[$columnIdx];
				$str = $requestColumn['search']['value'];
				if ($requestColumn['searchable'] == 'true' &&
					$str != ''
				) {
					$binding = self::bind($bindings, '%' . $str . '%', PDO::PARAM_STR);
					$columnSearch[] = $column['db'] . " LIKE " . $binding;
				}
			}
		}
		// Combine the filters into a single string
		$where = '';
		if (count($globalSearch)) {
			$where = '(' . implode(' OR ', $globalSearch) . ')';
		}
		if (count($columnSearch)) {
			$where = $where === '' ?
				implode(' AND ', $columnSearch) :
				$where . ' AND ' . implode(' AND ', $columnSearch);
		}
		if ($where !== '') {
			$where = 'WHERE ' . $where;
		}
		return $where;
	}

	static function bind (&$a, $val, $type) {
		$key = ':binding_' . count($a);
		$a[] = array(
			'key' => $key,
			'val' => $val,
			'type' => $type
		);
		return $key;
	}

	static function bind_values ($bindings) {
		$bind_values = [];

		if (is_array($bindings)) {
			for ($i = 0, $ien = count($bindings); $i < $ien; $i++) {
				$binding = $bindings[$i];
				$bind_values[$binding['key']] = $binding['val'];
			}
		}

		return $bind_values;
	}

	static function pluck ($a, $prop) {
		$out = array();
		for ($i = 0, $len = count($a); $i < $len; $i++) {
			$out[] = $a[$i][$prop];
		}
		return $out;
	}
}