<?php

namespace System\Core\Db;

use System\Core;
use Application\Config;

class DbCore extends \PDO {
	public function __construct($dbid = 0) {
    	if (count(Config\Database::$database)-1 >= $dbid) {
    		$config = Config\Database::$database[$dbid];
	        parent::__construct($config['dsn'], $config['username'], $config['password']);
	        try {
	            $this->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
	        }
	        catch (\PDOException $e) {
	            die($e->getMessage());
	        }
	    } else {
	    	return FALSE;
	    }
    }
}