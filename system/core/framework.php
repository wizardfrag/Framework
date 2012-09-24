<?php
namespace System\Core;

require_once 'system/core/log.php';
require_once 'system/core/exceptions.php';

class Framework {
	public static $config, $session, $db, $cache_loaded, $start_time;
	public static function init() {
		self::$start_time = microtime(true);
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
		Log::info($path);
		if (file_exists($path)) {
			require_once './' . $path;
		} else {
			Log::error('File not found');
			throw new \AutoloadException('Could not load ' . $class . ': ' . $path . ' not found!');
		}
	}

	public static function load_db() {
		Log::info('Loading db');
		self::$db = new Db\DbCore;
		Log::info('Db loaded');
	}

	public static function load_session() {
		Log::info('Loading session');
		self::$session = new Session;
		Log::info('Session loaded');
	}

	public static function load_cache() {
		Cache::init();
		self::$cache_loaded = true;
	}

	public static function process() {
		self::$config = new \Application\Config\Site;
		Log::init();
		Log::info("Framework loaded");
		if (isset(self::$config->autoload) && is_array(self::$config->autoload)) {
			foreach (self::$config->autoload as $module=>$autoload) {
				if ($autoload == true && method_exists('\System\Core\Framework', 'load_' . $module)) {
					call_user_func('self::load_' . $module);
				}
			}
		}
		\Application\Config\Routes::init();
		// See above TODO for the following 2 lines
		Cache::init();
		try {
			Router::route();
		} catch (\Http404Exception $e) {
			Router::show_404();
		}
		if (self::$cache_loaded)
			Cache::save();
		$load_time = microtime(true) - self::$start_time;
		Log::info("Framework end: " . sprintf('%.4f', $load_time) . " seconds");
 	}
}