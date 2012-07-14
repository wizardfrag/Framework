<?php
namespace System\Core;

class Router {
	protected static $url, $uri_parts, $controller, $routes;
	const DEFAULT_ROUTE = 'index';
	protected static $method = self::DEFAULT_ROUTE;
	public static function set_url($url = '') {
		self::$url = $url;
		if (substr(self::$url, 0, 1) === '/') {
			self::$url = substr(self::$url, 1);
		}
	}

	public static function add_route($match, $to) {
		self::$routes[$match] = $to;
	}

	public static function route($controller = '', $method = '') {
		self::$controller = $controller;

		if (!empty(self::$controller)) {
			if (!empty($method)) {
				self::$method = $method;
			}
			self::render();
		} else {
			try {
				if (!self::match_routes()) {
					self::parse_route(self::$url);
				}
			} catch (\AutoloadException $e) {
				Log::debug($e->getMessage());
				throw new \Http404Exception();
			}
		}
	}

	private static function match_routes() {
		// Largely adapted from CodeIgniter's Router
		if (isset(self::$routes[self::$url])) {
			// We have a literal match, so just do it!
			self::parse_route(self::$routes[self::$url]);
			return true;
		}

		foreach (self::$routes as $match => $to) {
			Log::debug('Checking route "' . $match . '" => "' . $to . '" against "' . self::$url . '"');
			// Translate CodeIgniter style wildcards to regex
			$match = str_replace(array(':any', ':num'), array('.+', '[0-9]+'), $match);


			if (preg_match('#^' . $match . '$#', self::$url)) {
				// We have a regex match

				if (strpos($to, '$') !== FALSE && strpos($match, '(') !== FALSE) {
					// Replace any back-references from the routes.
					$to = preg_replace('#^' . $match . '$#', $to, self::$url);
				}
				self::parse_route($to);
				return true;
			}
		}
		return FALSE; // We got nothing!
	}

	private static function parse_route($uri) {
		self::$uri_parts = array_filter(explode('/', $uri));

		if (count(self::$uri_parts) == 0) {
			// We just do the default route
			self::$controller = ucfirst(Framework::$config->default_route);
			self::$method = self::DEFAULT_ROUTE;
		} else {
			self::$controller = ucfirst(strtolower(reset(self::$uri_parts)));
			self::$method = (next(self::$uri_parts)) ?: self::DEFAULT_ROUTE;
		}

		// Set any remaining arguments
		$func_array = array();
		while ($var = next(self::$uri_parts)) {
			array_push($func_array, $var);
		}

		// Now we render out the controller
		self::render($func_array);
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