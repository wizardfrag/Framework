<?php
namespace System\Core;

include "system/core/loghandler.php";

/// Log Levels
// 1. Error
// 2. Debug
// 3. Info
// 4. All

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
		if (self::$log_level >= 2) {
			self::message('debug', $message);
		}
	}

	public static function error($message) {
		if (self::$log_level >= 1) {
			self::message('error', $message);
		}
	}

	public static function info($message) {
		if (self::$log_level >= 3) {
			self::message('info', $message);
		}
	}

	private static function message($level, $message) {
		if (!self::$log_handler) {
			self::$log_handler = new LogHandler;
		}

		$message = sprintf('%s [%s]: %s', date('Y-m-d H:i:s.') . sprintf("%04d", end(explode('.', microtime(true)))), $level, $message);
		self::$log_handler->write($message);
	}
}