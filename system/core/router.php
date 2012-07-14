<?php
namespace System\Core;

class Router {
	protected static $url, $uri_parts, $controller, $method;
	public static function set_url($url = '') {
		self::$url = $url;
	}

	public static function route($controller = '', $method = 'index') {
		self::$controller = $controller;
		self::$method = $method;

		if (!empty(self::$controller)) {
			self::render();
		} else {
			try {
				self::$uri_parts = array_filter(explode('/', self::$url));

				if (count(self::$uri_parts) == 0) {
					self::$controller = ucfirst(Framework::$config->default_route);
					self::$method = 'index';
				} else {
					self::$controller = ucfirst(strtolower(reset(self::$uri_parts)));
					self::$method = (next(self::$uri_parts)) ?: 'index';
				}
				$func_array = array();
				while ($var = next(self::$uri_parts)) {
					array_push($func_array, $var);
				}
				self::render($func_array);
			} catch (\AutoloadException $e) {
				Log::debug($e->getMessage());
				throw new \Http404Exception();
			}
		}
	}

	public static function render($args = array()) {
		$class_name = "Application\\Controllers\\" . self::$controller . "Controller";
		$controller = new $class_name;
		$method = self::$method;
		call_user_func_array(array($controller, self::$method), $args);
	}
	public static function show_404() {
		header($_SERVER['SERVER_PROTOCOL'] . ' 404 Not Found');
		if (isset(Framework::$config->controller_404)) {
			self::route(Framework::$config->controller_404);
			exit;
		}
		echo 'The page ' . self::$url . ' could not be found.<br/>';
		\System\Core\Log::debug(sprintf('Tried calling: %s->%s', self::$controller, self::$method));
		exit;
	}

	public static function teapot() {
		header($_SERVER['SERVER_PROTOCOL'] . " 418 I'm a teapot");
		echo "<h1>418 I'm a teapot</h1>\n<p>The server you tried to brew coffee on is a teapot</p>";
		exit;
	}

	public static function redirect($destination) {
		header($_SERVER['SERVER_PROTOCOL'] . " 302 Found");
		header("Location: " . $destination);
		echo "Click <a href=\"{$destination}\">here</a> if you are not forwarded automatically";
		exit;
	}
}