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
		if (method_exists($this, $name)) {
			call_user_func_array(array($this, $name), $arguments);
			var_dump($this->data);
		} elseif (method_exists($this, "_remap")) {
			array_unshift($arguments, $name);
			call_user_func_array(array($this, "_remap"), $arguments);
		} else {
			throw new \Http404Exception();
		}
	}
	public static function __callStatic($name, $arguments) {}
}