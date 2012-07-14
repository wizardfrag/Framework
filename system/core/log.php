<?php
namespace System\Core;

include "system/core/loghandler.php";

class Log {
	private static $log_level = 4;
	private static $log_handler;
	public static $display_log = true;

	public static function init() {
		self::$log_level = Framework::$config->log_level;
		self::$display_log = Framework::$config->display_log;
	}

	// Shortcuts
	public static function debug($message) {
		if (self::$log_level >= 4) {
			self::message('debug', $message);
		}
	}

	public static function error($message) {
		if (self::$log_level > 1) {
			self::message('error', $message);
		}
	}

	private static function message($level, $message) {
		if (!self::$log_handler) {
			self::$log_handler = new LogHandler;
		}

		$message = sprintf('%s [%s]: %s', date('Y-m-d H:i:s.') . sprintf("%.4f", microtime(true)), $level, $message);
		self::$log_handler->write($message);
	}
}