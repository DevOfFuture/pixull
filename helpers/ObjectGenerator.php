<?php

use Inflect\Inflect;

class ObjectGenerator {
	private static $name_singular;
	private static $name_plural;
	private static $name_pascal;
	private static $name_variable;

	static function set_names ($name) {
		// set names used for string replacement
		self::$name_singular = ucwords($name);
		self::$name_plural = Inflect::pluralize(self::$name_singular);
		self::$name_pascal = str_replace(array(' ', '-', '_'), '', self::$name_singular);
		self::$name_variable = strtolower(str_replace(array(' ', '-'), '_', self::$name_singular));
	}

	static function object_exists () {
		// check if object exists by model file name
		return file_exists('models/'.self::$name_pascal.'.php');
	}

	static function create_db_table () {
		// create db table if it doesn't already exist
		$stm = DB::get_pdo()->prepare('
			CREATE TABLE IF NOT EXISTS `'.self::$name_variable.'` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `name` varchar(255) NOT NULL,
			  PRIMARY KEY (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;
		');
		$stm->execute();
	}

	static function add_content_before ($filename, $hook_comment, $content) {
		// add content to file before hook comment
		$file_content = file_get_contents($filename);
		$new_file_content = str_replace($hook_comment, $content.$hook_comment, $file_content);

		file_put_contents($filename, $new_file_content);
	}

	static function add_permissions () {
		// add permissions line to app config file
		$filename = 'config/app.php';
		$hook_comment = '// generator hook (do not remove or modify this comment)';
		$content = "['Create ".self::$name_plural."', 'Read ".self::$name_plural."', 'Update ".self::$name_plural."', 'Delete ".self::$name_plural."'],".PHP_EOL."	";
		
		self::add_content_before($filename, $hook_comment, $content);
	}
	
	static function add_routes () {
		// add routes to routes file
		$filename = 'routes.php';
		$hook_comment = '// generator hook (do not remove or modify this comment)';
		$content = "// ".self::$name_singular." routes".PHP_EOL;
		$content .= "Router::set_route('".self::$name_variable."', '".self::$name_pascal."Controller::index', 'signed_in', 'Read ".self::$name_plural."');".PHP_EOL;
		$content .= "Router::set_route('".self::$name_variable."/datatable', '".self::$name_pascal."Controller::datatable', 'signed_in', 'Read ".self::$name_plural."');".PHP_EOL;
		$content .= "Router::set_route('".self::$name_variable."/create', '".self::$name_pascal."Controller::create', 'signed_in', 'Create ".self::$name_plural."');".PHP_EOL;
		$content .= "Router::set_route('".self::$name_variable."/update/(\d+)', '".self::$name_pascal."Controller::update', 'signed_in', 'Update ".self::$name_plural."');".PHP_EOL;
		$content .= "Router::set_route('".self::$name_variable."/delete/(\d+)', '".self::$name_pascal."Controller::delete', 'signed_in', 'Delete ".self::$name_plural."');".PHP_EOL.PHP_EOL;

		self::add_content_before($filename, $hook_comment, $content);
	}
	
	static function add_menu_item () {
		$filename = 'views/app_header.php';
		$hook_comment = '<!-- generator hook (do not remove or modify this comment) -->';
		$content = "<?php if (AppUser::has_permission('Read ".self::$name_plural."')): ?><li<?php if (Viewer::get_param('title') == '".self::$name_plural."') echo ' class=\"active\"'; ?>><a href=\"<?php echo APP_URL; ?>/".self::$name_variable."\"><i class=\"fa fa-dot-circle-o\"></i> <span>".self::$name_plural."</span></a></li><?php endif; ?>".PHP_EOL."					";

		self::add_content_before($filename, $hook_comment, $content);
	}

	static function create_model () {
		// create model with replaced string names
		file_put_contents('models/'.self::$name_pascal.'.php', self::replace_names(file_get_contents('models/GeneratedObject.php')));
	}

	static function create_views () {
		// create views with replaced string names
		file_put_contents('views/'.self::$name_variable.'_create.php', self::replace_names(file_get_contents('views/generated_object_create.php')));
		file_put_contents('views/'.self::$name_variable.'_datatable.php', self::replace_names(file_get_contents('views/generated_object_datatable.php')));
		file_put_contents('views/'.self::$name_variable.'_index.php', self::replace_names(file_get_contents('views/generated_object_index.php')));
		file_put_contents('views/'.self::$name_variable.'_update.php', self::replace_names(file_get_contents('views/generated_object_update.php')));
	}

	static function create_controller () {
		// create controller with replaced string names
		file_put_contents('controllers/'.self::$name_pascal.'Controller.php', self::replace_names(file_get_contents('controllers/GeneratedObjectController.php')));
	}

	static function replace_names ($content) {
		// replace strings with their new values
		$replace_array = [
			'// file used by generator, do not delete this unless you never use the object generator'.PHP_EOL.PHP_EOL => '',
			'Generated Objects' => self::$name_plural,
			'Generated Object' => self::$name_singular,
			'GeneratedObject' => self::$name_pascal,
			'generated_object' => self::$name_variable,
		];

		foreach ($replace_array as $search => $replace) {
			$content = str_replace($search, $replace, $content);
		}

		return $content;
	}
}