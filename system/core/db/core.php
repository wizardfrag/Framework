<?php

namespace System\Core\Db;

use System\Core;

class Core {
	protected $dbconn;

	public static function init() {
		self::$dbconn = self::connect();
	}

	public static function get() {
		return self::$dbconn;
	}
}