<?php

namespace System\Core;

class Controller {
	protected $data, $framework, $viewname;
	public function __construct() {
		Log::debug("Controller initialised");
	}

	public function index() {}

	public function __call($name, $arguments) {
		Log::debug('Controller method called: ' . __CLASS__ . '\\' . $name . '(' . implode(", ", $arguments) . ')');
		if (method_exists($this, "_remap")) {
			// We need to add the method name to the arguments and call the _remap
			// method on the controller if the method doesn't exist but _remap does
			array_unshift($arguments, $name);
			call_user_func_array(array($this, "_remap"), $arguments);
		} else {
			// Nothing to see here, 404
			throw new \Http404Exception();
		}
	}

	public static function __callStatic($name, $arguments) {
		throw new Exception('Controllers must not be called statically');
		return false;
	}
}