<?php

namespace System\Core;
use \System\Helpers\UrlHelper;
use \System\Helpers\FormHelper;

class View {
	public function render($filename, $vars = array()) {
		$extension = pathinfo($filename, PATHINFO_EXTENSION);

		if (!$extension) $extension = 'php';
		$view_file = 'application/views/' . $filename . '.' . $extension;
		if (file_exists($view_file)) {
			$_view = array();
			$_view['request_url'] = Router::get_url();
			$_view['site_url'] = Framework::$config->site_url;
			extract($vars);
			include($view_file);
		} else {
			throw new \ViewNotFoundException($view_file);
		}
	}
}