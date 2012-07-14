<?php

namespace Application\Config;

class Database extends System\Core\Db\Config {
	public static $dsn = 'mysql:dbname=;host=';
	public static $username = '';
	public static $password = '';
}