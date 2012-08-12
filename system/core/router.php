<?php
namespace System\Core;

class Router {
	protected static $url, $uri_parts, $format,
		$controller, $routes = array('index' => ''),
		$default_controller, $controller_404 = false,
		$rendering_404 = false;

	const DEFAULT_METHOD = 'index';

	protected static $method = self::DEFAULT_METHOD;

	public static function default_controller($controller) {
		self::$default_controller = $controller;
	}

	public static function set_404($controller) {
		self::$controller_404 = $controller;	
	}

	public static function set_url($url = '') {
		self::$url = $url;
		$path_parts = pathinfo(self::$url);
		self::$url = $path_parts['dirname'] . '/' . $path_parts['filename'];
		self::$url = str_replace('//', '/', self::$url);
		self::$format = (isset($path_parts['extension'])) ? $path_parts['extension'] : 'html';
		if (substr(self::$url, 0, 1) === '/') {
			self::$url = substr(self::$url, 1);
		}
	}

	public static function get_url() {
		return self::$url;
	}

	public static function add_route($match, $to) {
		self::$routes[$match] = $to;
	}

	public static function route($controller = '', $method = '') {
		self::$controller = $controller;

		if (!empty(self::$controller)) {
			if (!empty($method)) {
				self::$method = $method;
			} else {
				self::$method = self::DEFAULT_METHOD;
			}
			self::render();
		} else {
			try {
				self::match_routes();
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
			// Log::debug('Checking route "' . $match . '" => "' . $to . '" against "' . self::$url . '"');
			// Translate CodeIgniter style wildcards to regex
			$match = str_replace(array(':any', ':num'), array('.+', '[0-9]+'), $match);


			if (preg_match('#^' . $match . '$#', self::$url)) {
				// We have a regex match

				if (strpos($to, '$') !== false && strpos($match, '(') !== false) {
					// Replace any back-references from the routes.
					$to = preg_replace('#^' . $match . '$#', $to, self::$url);
				}
				self::parse_route($to);
				return true;
			}
		}
		self::parse_route(self::$url);
		return false; // We got nothing!
	}

	private static function parse_route($uri) {
		self::$uri_parts = array_filter(explode('/', $uri));

		if (count(self::$uri_parts) == 0) {
			// We just do the default route
			self::$controller = ucfirst(self::$default_controller);
			self::$method = self::DEFAULT_METHOD;
		} else {
			self::$controller = ucfirst(strtolower(reset(self::$uri_parts)));
			self::$method = (next(self::$uri_parts)) ?: self::DEFAULT_METHOD;
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
		$controller->set_view(strtolower(self::$controller) . DIRECTORY_SEPARATOR . $method);
		ob_start();
		call_user_func_array(array($controller, self::$method), $args);
		$controller_output = ob_get_clean();
		if ($controller->should_render() == true) {
			if (self::$format == 'json') {
				header("Content-Type: application/json");
				echo json_encode($controller->get_data());
				exit;
			} elseif (self::$format == 'html') {
				$viewfile = ($controller->get_view()) ?: 'test';
				$data = (array)$controller->get_data();
				$data['controller_output'] = $controller_output;
				View::render($viewfile, $data);
			} else {
				self::show_404();
			}
		} else {
			print $controller_output;
		}
	}
	public static function show_404() {
		header($_SERVER['SERVER_PROTOCOL'] . ' 404 Not Found');
		if (self::$controller_404 && !self::$rendering_404) {
			self::$rendering_404 = true; // Catch an endless 404 loop if the 404
										 // controller's index method doesn't exist!
			self::route(self::$controller_404);
			exit;
		}
		echo "<h1>404 Not Found</h1>\n" .
			 'The page /' . self::$url . ' could not be found.<br/>';
		exit;
	}

	public static function bad_request() {
		header($_SERVER['SERVER_PROTOCOL'] . " 400 Bad Request");
		echo "<h1>400 Bad Request</h1>\n<p>Your browser sent a request that we were unable to process</p>";
	}

	public static function teapot() {
		header($_SERVER['SERVER_PROTOCOL'] . " 418 I'm a teapot");
		echo "<h1>418 I'm a teapot</h1>\n<p>The server you tried to brew coffee on is a teapot</p>";
		exit;
	}

	public static function redirect($destination) {
		if (substr($destination, 0, 4) != 'http' && substr($destination, 0, 3) != 'ftp') {
			$destination = Framework::$config->site_url . $destination;
		}

		header($_SERVER['SERVER_PROTOCOL'] . " 302 Found");
		header("Location: " . $destination);
		echo "Click <a href=\"{$destination}\">here</a> if you are not forwarded automatically";
		exit;
	}

	public static function get_format() {
		return self::$format;
	}
}