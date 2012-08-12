<?php
namespace System\Core;

class Model {
	private $table;
	public function __construct() {
		Log::debug('Model called: ' . __CLASS__ . ' ' . get_class());
		$this->table = end(explode('\\', strtolower(get_called_class())));
		if (substr($this->table, -1) != 's') {
			$this->table .= 's';
		}
	}
}