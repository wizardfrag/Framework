<?php
namespace System\Core;

class Input {
	public static function get($name) {
		if (array_key_exists($name, $_GET)) {
			return self::sanitize_input($name);
		} else {
			return false;
		}
	}

	public static function post($name) {
		if (array_key_exists($name, $_POST)) {
			return self::sanitize_input($name, INPUT_POST);
		} else {
			return false;
		}
	}

	public static function get_post($name, $first = INPUT_POST) {
		if ($first == INPUT_POST) {
			if (self::post($name)) {
				return self::post($name);
			} else {
				return self::get($name);
			}
		} else {
			if (self::get($name)) {
				return self::get($name);
			} else {
				return self::post($name);
			}
		}
	}

	public static function sanitize_input($name, $type = INPUT_GET) {
		return filter_input($type, $name, FILTER_SANITIZE_STRING);
	}

	public static function sanitize($value) {
		return filter_var($value, FILTER_SANITIZE_STRING);
	}
}