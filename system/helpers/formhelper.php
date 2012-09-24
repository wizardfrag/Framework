<?php

namespace System\Helpers;
use \System\Helpers\UrlHelper;
use \System\Core\Framework;
use \System\Core\Router;

class FormHelper {
	public function open( $target = '', $method = 'get' ) {
		if ($target == '') {
			$target = Router::get_url();
		}
		if (substr($target, 0, 4) != 'http') {
			$target = UrlHelper::site_url($target);
		}

		return '<form method="' . $method . '" action="' . $target . '">';
	}

	public function close() {
		echo '</form>';
	}

	public function select($name, $options) {
		$ret = '';
		$ret .= '<select name="' . $name . '">';
		foreach ($options as $key=>$value) {
			$ret .= self::option($key, $value);
		}
		$ret .= '</select>';
		return $ret;
	}

	public function option($option, $display = '') {
		return '<option value="' . $option . '">' . $display . '</option>';
	}

	public function submit($name = 'submit', $value = 'Submit') {
		return '<input type="submit" name="' . $name . '" value="' . $value . '"/>';
	}
}