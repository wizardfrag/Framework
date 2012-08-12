<?php
namespace System\Core;

require_once 'system/core/log.php';
require_once 'system/core/exceptions.php';

class Framework {
	public static $config, $session, $db;
	public static function init() {
		spl_autoload_register(__NAMESPACE__ . '\Framework::autoload');
	}

	public static function autoload($class) {
		$parts = explode('\\', $class);
		// Filenames and folders are lowercase
		$class = array_pop($parts);
		$filename = strtolower($class) . '.php';

		foreach ($parts as &$part)
			$part = strtolower($part);

		$path = implode('/', $parts);
		$path .= '/' . $filename;
		Log::debug($path);
		if (file_exists($path)) {
			require_once './' . $path;
		} else {
			Log::debug('File not found');
			throw new \AutoloadException('Could not load ' . $class . ': ' . $path . ' not found!');
		}
	}

	public static function process() {
		self::$config = new \Application\Config\Site;
		// TODO: Add some checks of the config/etc.
		//       to see if we actually want sessions...
		self::$session = new Session;
		self::$db = new Db\DbCore;
		\Application\Config\Routes::init();
		// See above TODO for the following 2 lines
		Log::init();
		Cache::init();
		try {
			Router::route();
		} catch (\Http404Exception $e) {
			Router::show_404();
		}
		// TODO: Check to see if we're still saving cache
		Cache::save();
 	}
}