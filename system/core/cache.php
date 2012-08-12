<?php
namespace System\Core;

class Cache {
	private static $cache;
	public static function init() {
		self::$cache = new FileCache;
	}
	public static function load() {
		self::$cache->load();
	}
	public static function set($name, $value, $expiry = 300) {
		self::$cache->set($name, $value, $expiry);
	}
	public static function get($name) {
		return self::$cache->get($name);
	}
	public function check($name) {
		return self::$cache->check($name);
	}
	public static function remove($name) {
		self::$cache->remove($name);
	}
	public static function save() {
		self::$cache->save();
	}
}

class FileCache implements CacheInterface {
	private $cache_file, $contents;
	public function __construct() {
		$this->cache_file = FCPATH . '/application/cache/cache_file';
		$this->load();
	}

	public function load() {
		if (file_exists($this->cache_file)) {
			$f = file_get_contents($this->cache_file);
			$this->contents = unserialize($f);
		} else {
			$this->contents = array();
		}
		foreach ($this->contents as $key=>$variable) {
			if (array_key_exists('expiry', $variable)) {
				if ($variable['expiry'] < time()) {
					unset($this->contents[$key]);
				}
			}
		}
	}

	public function set($name, $value, $expiry) {
		$this->contents[$name] = array(
			'expiry' => time() + $expiry,
			'value' => $value
		);
	}

	public function get($name) {
		if (array_key_exists($name, $this->contents) && array_key_exists('value', $this->contents[$name])) {
			return $this->contents[$name]['value'];
		} else {
			return NULL;
		}
	}

	public function check($name) {
		if (array_key_exists($name, $this->contents) && array_key_exists('value', $this->contents[$name])) {
			return true;
		} else {
			return false;
		}
	}

	public function remove($name) {
		if (array_key_exists($name, $this->contents))
			unset($this->contents[$name]);
	}

	public function save() {
		$f = serialize($this->contents);
		file_put_contents($this->cache_file, $f);
	}
}

interface CacheInterface {
	public function __construct();
	public function load();
	public function set($name, $value, $expiry);
	public function get($name);
	public function check($name);
	public function remove($name);
	public function save();
}