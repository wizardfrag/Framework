<?php

namespace System\Core\Db;

use System\Core;

class Core {
	protected $dbconn;

	public function __construct() {

	}

	public static function init() {
		self::$dbconn = self::connect();
	}
}