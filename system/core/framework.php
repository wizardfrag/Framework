<?php
namespace System\Core;

require_once 'system/core/log.php';
require_once 'system/core/exceptions.php';

class Framework {
	public static $config;
	public static function init() {
		spl_autoload_register(__NAMESPACE__ . '\Framework::log');
		spl_autoload_register(__NAMESPACE__ . '\Framework::autoload');
	}

	public static function log($class) {
		Log::debug('Loading: ' . $class);
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
		try {
			self::$config = new \Application\Config\Site;
		} catch (\AutoloadException $e) {
			self::$config = new \System\Core\Config;
		}
		Log::init();
		try {
			Router::route();
		} catch (\Http404Exception $e) {
			Router::show_404();
		}
 	}
}