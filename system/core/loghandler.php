<?php

namespace System\Core;

include "system/core/FirePHP.class.php";

class LogHandler {
	protected $fd, $firephp;

	public function __construct() {
		$file_name = 'application/log/' . date('Y-m-d') . '.log';
		if (is_writable(dirname($file_name))) {
			$this->fd = fopen($file_name, 'a+');
			$this->firephp = \FirePHP::getInstance(true);
		}
	}

	public function write($msg) {
		if ($this->fd)
			fwrite($this->fd, $msg . "\n");
		if (Log::$display_log)
			$this->firephp->log(addslashes($msg));
	}
	public function __destruct() {
		fclose($this->fd);
	}
}