<?php

namespace System\Core;

class Controller {
	protected $framework, $viewname, $render = true, $data = array();
	public function __construct() {
		Log::debug("Controller initialised");
	}

	public function __call($name, $arguments) {
		Log::debug('Controller method called: ' . get_called_class() . '->' . $name . '(' . implode(", ", $arguments) . ') but did not exist');
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

	public function should_render() {
		return $this->render;
	}

	public function get_data() {
		return $this->data;
	}
}