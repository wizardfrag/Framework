<?php
namespace System\Helpers;
use System\Core\Framework;

class UrlHelper {
	public function site_url( $path = '' ) {
		$url = Framework::$config->site_url;

		if (substr($url, -1) == '/') {
			$url = substr($url, 0, -1);
		}
		if (substr($path, 0, 1) == '/') {
			$path = substr($path, 1);
		}

		return implode('/', array($url, $path));
	}
}