<?php
namespace System\Core;

class Session {
	protected $session_id, $data, $flash, $flashtmp;
	public function __construct() {
		session_start();
		$this->session_id = session_id();
		if (!isset($_SESSION['data'])) {
			$_SESSION['data'] = array();
		}
		if (!isset($_SESSION['flash'])) {
			$_SESSION['flash'] = array();
		}
		$this->data = $_SESSION['data'];
		$this->flashtmp = $this->flash = $_SESSION['flash'];
		$this->flash = array();
	}

	public function __get($name) {
		return $this->get($name);
	}

	public function __set($name, $value) {
		return $this->set($name, $value);
	}

	public function __isset($name) {
		return (isset($this->data[$name]) OR isset($this->flash[$name]) OR isset($this->flashtmp[$name]));
	}

	public function set($name, $value) {
		$this->data[$name] = $value;
	}

	public function get($name) {
		$ret = false;
		if (isset($this->data[$name])) {
			$ret = $this->data[$name];
		} elseif (isset($this->flash[$name])) {
			$ret = $this->flash[$name];
		} elseif (isset($this->flashtmp[$name])) {
			$ret = $this->flashtmp[$name];
		}
		return $ret;
	}

	public function get_flash($name) {
		$ret = false;
		if (isset($this->flash[$name])) {
			$ret = $this->flash[$name];
		} elseif (isset($this->flashtmp[$name])) {
			$ret = $this->flashtmp[$name];
		}
		return $ret;
	}

	public function remove($name) {
		if (isset($this->data[$name])) { 
			unset($this->data[$name]);
			return true;
		}
		return false;
	}

	public function set_flash($name, $value) {
		$this->flash[$name] = $value;
	}

	public function __destruct() {
		$_SESSION['data'] = $this->data;
		$_SESSION['flash'] = $this->flash;
	}

	public function destroy() {
		$this->data = array();
		$this->flashtmp = array();
	}
}